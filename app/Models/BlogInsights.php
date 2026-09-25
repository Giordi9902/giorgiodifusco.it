<?php

namespace App\Models;

use Core\BlogContent;
use PDOException;

/**
 * Statistiche del blog: conteggio delle visite (aggregate per giorno, senza dati
 * personali) e analisi per la dashboard admin.
 *
 * Le tabelle arrivano con schemas/migrations/2026-09-25-blog-insights.sql: finché
 * la migrazione non è stata eseguita il tracciamento non fa nulla e
 * isAvailable() restituisce false.
 */
class BlogInsights extends Model
{
    protected $table = 'blog_post_views';

    private const BOT_PATTERN = '/bot|crawl|spider|slurp|mediapartners|facebookexternalhit|embedly|preview|lighthouse|headless|curl|wget|python|java\/|go-http|okhttp|monitor|uptime|scan/i';

    // ── Tracciamento ────────────────────────────────────────────────────────

    /**
     * Conta una visita all'articolo. Esclusi: bot, admin loggati e ricariche
     * della stessa pagina nella stessa sessione e nello stesso giorno.
     */
    public function trackView(int $postId): void
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($ua === '' || preg_match(self::BOT_PATTERN, $ua) || ($_SESSION['user_role'] ?? null) === 'ADMIN') {
            return;
        }

        $today = date('Y-m-d');
        if (($_SESSION['blog_viewed'][$postId] ?? null) === $today) {
            return;
        }
        $_SESSION['blog_viewed'][$postId] = $today;

        try {
            $this->db->prepare(
                "INSERT INTO blog_post_views (post_id, view_date, views) VALUES (?, ?, 1)
                 ON DUPLICATE KEY UPDATE views = views + 1"
            )->execute([$postId, $today]);

            $this->db->prepare(
                "INSERT INTO blog_traffic_sources (view_date, source, views) VALUES (?, ?, 1)
                 ON DUPLICATE KEY UPDATE views = views + 1"
            )->execute([$today, self::classifySource($_SERVER['HTTP_REFERER'] ?? '')]);
        } catch (PDOException $e) {
            // tabelle non ancora create: la pagina dell'articolo deve funzionare comunque
            error_log('BlogInsights::trackView: ' . $e->getMessage());
        }
    }

    /**
     * Sorgente leggibile a partire dal referrer (solo il dominio, mai l'URL completo).
     */
    public static function classifySource(string $referer): string
    {
        $host = strtolower((string) parse_url($referer, PHP_URL_HOST));
        if ($host === '') {
            return 'Diretto';
        }

        $own = strtolower((string) parse_url(BASE_URL, PHP_URL_HOST));
        $strip = fn(string $h) => preg_replace('/^(www|m|l|lm)\./', '', $h);
        if ($strip($host) === $strip($own)) {
            return 'Interno al sito';
        }

        $known = [
            '/(^|\.)google\./'                          => 'Google',
            '/(^|\.)bing\.com$/'                        => 'Bing',
            '/(^|\.)duckduckgo\.com$/'                  => 'DuckDuckGo',
            '/(^|\.)(yahoo|ecosia|qwant|yandex)\./'     => 'Altri motori di ricerca',
            '/(^|\.)(instagram\.com)$/'                 => 'Instagram',
            '/(^|\.)(facebook\.com|fb\.com)$/'          => 'Facebook',
            '/(^|\.)linkedin\.com$|^lnkd\.in$/'         => 'LinkedIn',
            '/(^|\.)(t\.co|twitter\.com|x\.com)$/'      => 'X / Twitter',
            '/(^|\.)reddit\.com$/'                      => 'Reddit',
            '/(^|\.)(whatsapp\.com|wa\.me)$/'           => 'WhatsApp',
            '/(^|\.)(telegram\.org|t\.me)$/'            => 'Telegram',
            '/(^|\.)github\.com$/'                      => 'GitHub',
            '/(^|\.)(youtube\.com|youtu\.be)$/'         => 'YouTube',
            '/(^|\.)chatgpt\.com$|(^|\.)openai\.com$/'  => 'ChatGPT',
            '/(^|\.)(claude\.ai|perplexity\.ai)$/'      => 'Assistenti AI',
        ];
        foreach ($known as $pattern => $label) {
            if (preg_match($pattern, $host)) {
                return $label;
            }
        }

        return mb_substr($strip($host), 0, 100);
    }

    // ── Insights per la dashboard ───────────────────────────────────────────

    public function isAvailable(): bool
    {
        try {
            $this->db->query("SELECT 1 FROM blog_post_views LIMIT 1");
            $this->db->query("SELECT 1 FROM blog_traffic_sources LIMIT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Tutti i dati della sezione "Insights blog" per un periodo di $days giorni (oggi incluso).
     */
    public function getDashboard(int $days): array
    {
        $today = new \DateTimeImmutable('today');
        $from  = $today->modify('-' . ($days - 1) . ' days');
        $prevFrom = $from->modify("-{$days} days");
        $prevTo   = $from->modify('-1 day');

        $posts = $this->db->query(
            "SELECT bp.id, bp.title, bp.slug, bp.status, bp.excerpt, bp.content, bp.seo_description,
                    bp.featured_image_id, bp.course_id, bp.subject_id, bp.published_at, bp.created_at,
                    bp.updated_at, c.name AS course_name
             FROM blog_posts bp
             LEFT JOIN courses c ON c.id = bp.course_id
             ORDER BY bp.created_at DESC"
        )->fetchAll();

        $published = array_values(array_filter($posts, fn($p) => $p['status'] === 'published'));
        $drafts    = array_values(array_filter($posts, fn($p) => $p['status'] !== 'published'));

        $data = [
            'days'             => $days,
            'from'             => $from->format('Y-m-d'),
            'to'               => $today->format('Y-m-d'),
            'available'        => $this->isAvailable(),
            'published_count'  => count($published),
            'draft_count'      => count($drafts),
            'published_in_period' => count(array_filter(
                $published,
                fn($p) => $p['published_at'] && $p['published_at'] >= $from->format('Y-m-d')
            )),
            'avg_words'        => $published
                ? (int) round(array_sum(array_map(fn($p) => BlogContent::wordCount($p['content']), $published)) / count($published))
                : 0,
            'health'           => $this->contentHealth($published, $drafts),
            'by_course'        => $this->postsByCourse(),
            'views_total'      => 0,
            'views_prev'       => 0,
            'views_all_time'   => 0,
            'daily'            => [],
            'top_posts'        => [],
            'sources'          => [],
            'zero_view_posts'  => [],
        ];

        if (!$data['available']) {
            return $data;
        }

        // visite per giorno, con i giorni senza visite a zero
        $stmt = $this->db->prepare(
            "SELECT view_date, SUM(views) AS views FROM blog_post_views
             WHERE view_date BETWEEN ? AND ? GROUP BY view_date"
        );
        $stmt->execute([$from->format('Y-m-d'), $today->format('Y-m-d')]);
        $byDay = array_column($stmt->fetchAll(), 'views', 'view_date');
        for ($d = $from; $d <= $today; $d = $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $data['daily'][] = ['date' => $key, 'views' => (int) ($byDay[$key] ?? 0)];
        }
        $data['views_total'] = array_sum(array_column($data['daily'], 'views'));

        $stmt = $this->db->prepare("SELECT COALESCE(SUM(views), 0) FROM blog_post_views WHERE view_date BETWEEN ? AND ?");
        $stmt->execute([$prevFrom->format('Y-m-d'), $prevTo->format('Y-m-d')]);
        $data['views_prev'] = (int) $stmt->fetchColumn();

        $data['views_all_time'] = (int) $this->db->query("SELECT COALESCE(SUM(views), 0) FROM blog_post_views")->fetchColumn();

        // articoli più letti nel periodo (con il totale di sempre per confronto)
        $stmt = $this->db->prepare(
            "SELECT bp.id, bp.title, bp.slug, SUM(v.views) AS views,
                    (SELECT SUM(v2.views) FROM blog_post_views v2 WHERE v2.post_id = bp.id) AS views_all_time
             FROM blog_post_views v
             JOIN blog_posts bp ON bp.id = v.post_id
             WHERE v.view_date BETWEEN ? AND ?
             GROUP BY bp.id, bp.title, bp.slug
             ORDER BY views DESC
             LIMIT 8"
        );
        $stmt->execute([$from->format('Y-m-d'), $today->format('Y-m-d')]);
        $data['top_posts'] = $stmt->fetchAll();

        // sorgenti di traffico
        $stmt = $this->db->prepare(
            "SELECT source, SUM(views) AS views FROM blog_traffic_sources
             WHERE view_date BETWEEN ? AND ?
             GROUP BY source ORDER BY views DESC LIMIT 8"
        );
        $stmt->execute([$from->format('Y-m-d'), $today->format('Y-m-d')]);
        $data['sources'] = $stmt->fetchAll();

        // visite per area nel periodo
        $stmt = $this->db->prepare(
            "SELECT bp.course_id, SUM(v.views) AS views
             FROM blog_post_views v JOIN blog_posts bp ON bp.id = v.post_id
             WHERE v.view_date BETWEEN ? AND ?
             GROUP BY bp.course_id"
        );
        $stmt->execute([$from->format('Y-m-d'), $today->format('Y-m-d')]);
        $viewsByCourse = array_column($stmt->fetchAll(), 'views', 'course_id');
        foreach ($data['by_course'] as &$row) {
            $row['views'] = (int) ($viewsByCourse[$row['course_id'] ?? ''] ?? 0);
        }
        unset($row);

        // articoli pubblicati da più di 7 giorni e mai letti nel periodo
        $stmt = $this->db->prepare(
            "SELECT DISTINCT post_id FROM blog_post_views WHERE view_date BETWEEN ? AND ?"
        );
        $stmt->execute([$from->format('Y-m-d'), $today->format('Y-m-d')]);
        $viewed = array_flip($stmt->fetchAll(\PDO::FETCH_COLUMN));
        $weekAgo = $today->modify('-7 days')->format('Y-m-d');
        foreach ($published as $p) {
            if (!isset($viewed[$p['id']]) && ($p['published_at'] ?? $p['created_at']) < $weekAgo) {
                $data['zero_view_posts'][] = ['id' => $p['id'], 'title' => $p['title'], 'slug' => $p['slug']];
            }
        }

        return $data;
    }

    /**
     * Controlli SEO/editoriali: per ogni problema, gli articoli da sistemare.
     */
    private function contentHealth(array $published, array $drafts): array
    {
        $pick = fn(array $posts, callable $test) => array_values(array_map(
            fn($p) => ['id' => $p['id'], 'title' => $p['title']],
            array_filter($posts, $test)
        ));

        $staleLimit = (new \DateTimeImmutable('-30 days'))->format('Y-m-d H:i:s');

        return [
            [
                'label' => 'Senza meta description',
                'hint'  => 'Google mostra un testo a caso nei risultati.',
                'posts' => $pick($published, fn($p) => trim((string) $p['seo_description']) === ''),
            ],
            [
                'label' => 'Senza estratto',
                'hint'  => 'Le card del blog usano l\'inizio del testo.',
                'posts' => $pick($published, fn($p) => trim((string) $p['excerpt']) === ''),
            ],
            [
                'label' => 'Senza copertina',
                'hint'  => 'Niente immagine nelle card e nelle anteprime social.',
                'posts' => $pick($published, fn($p) => empty($p['featured_image_id'])),
            ],
            [
                'label' => 'Senza area o argomento',
                'hint'  => 'Non compaiono nei filtri del blog.',
                'posts' => $pick($published, fn($p) => empty($p['course_id']) || empty($p['subject_id'])),
            ],
            [
                'label' => 'Troppo brevi (< 300 parole)',
                'hint'  => 'Contenuti corti posizionano peggio.',
                'posts' => $pick($published, fn($p) => BlogContent::wordCount($p['content']) < 300),
            ],
            [
                'label' => 'Bozze ferme da oltre 30 giorni',
                'hint'  => 'Da finire o eliminare.',
                'posts' => $pick($drafts, fn($p) => ($p['updated_at'] ?? $p['created_at']) < $staleLimit),
            ],
        ];
    }

    private function postsByCourse(): array
    {
        return $this->db->query(
            "SELECT bp.course_id, COALESCE(c.name, 'Senza area') AS name,
                    SUM(bp.status = 'published') AS published, SUM(bp.status <> 'published') AS drafts
             FROM blog_posts bp
             LEFT JOIN courses c ON c.id = bp.course_id
             GROUP BY bp.course_id, c.name
             ORDER BY published DESC, name ASC"
        )->fetchAll();
    }
}
