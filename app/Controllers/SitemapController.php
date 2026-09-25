<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\BlogPost;

class SitemapController extends Controller
{
    /**
     * Genera la sitemap XML delle pagine pubbliche (homepage, blog, articoli).
     * Se il DB non risponde la sitemap contiene comunque le pagine statiche.
     */
    public function index()
    {
        header('Content-Type: application/xml; charset=utf-8');

        $urls = [
            [
                'loc'        => BASE_URL . '/',
                'changefreq' => 'weekly',
                'priority'   => '1.0',
            ],
            [
                'loc'        => BASE_URL . '/blog',
                'changefreq' => 'daily',
                'priority'   => '0.8',
            ],
        ];

        foreach ($this->fetchPublishedPostsSafely() as $post) {
            $lastmod = $post['updated_at'] ?? $post['published_at'] ?? $post['created_at'];

            $urls[] = [
                'loc'        => BASE_URL . '/blog/' . $post['slug'],
                'lastmod'    => $lastmod ? date('Y-m-d', strtotime($lastmod)) : null,
                'changefreq' => 'monthly',
                'priority'   => '0.6',
            ];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($url['loc'], ENT_QUOTES | ENT_XML1, 'UTF-8') . "</loc>\n";
            if (!empty($url['lastmod'])) {
                echo '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            }
            echo '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            echo '    <priority>' . $url['priority'] . "</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
    }

    private function fetchPublishedPostsSafely(): array
    {
        try {
            return (new BlogPost())->findAllPublishedForSitemap();
        } catch (\PDOException $e) {
            error_log('Sitemap: blog non disponibile - ' . $e->getMessage());
            return [];
        }
    }
}
