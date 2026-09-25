<?php
// --- SEO & meta vars (set before header) ---
$seoTitleRaw     = (isset($post['seo_title']) && $post['seo_title'] !== '') ? $post['seo_title'] : ($post['title'] ?? '');
$pageTitle       = $seoTitleRaw . ' | GiorgioDiFusco.it';
$metaDescription = !empty($post['seo_description']) ? $post['seo_description'] : (!empty($post['excerpt']) ? mb_substr(strip_tags($post['excerpt']), 0, 160) : null);
$metaKeywords    = $post['seo_keywords'] ?? null;
$canonicalUrl    = BASE_URL . '/blog/' . ($post['slug'] ?? '');
$ogType          = 'article';
$ogTitle         = $seoTitleRaw;
$ogDescription   = $metaDescription;
$ogImage         = (!empty($featuredImage['path'])) ? BASE_URL . '/' . $featuredImage['path'] : null;

// --- Content parser ---
function parse_video_url(string $url): string
{
    if (preg_match('/(?:youtube\.com\/watch\?(?:.*&)?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
        return 'https://www.youtube-nocookie.com/embed/' . $m[1];
    }
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    return '';
}

function apply_inline(string $esc): string
{
    // images ![alt](url) — with lazy loading
    $esc = preg_replace(
        '/!\[([^\]]*)]\(([^)]+)\)/',
        '<img src="$2" alt="$1" class="my-4 rounded-xl border border-gray-200 max-w-full shadow-sm" loading="lazy" decoding="async">',
        $esc
    );
    // links [text](url)
    $esc = preg_replace(
        '/\[([^\]]+)]\(([^)]+)\)/',
        '<a href="$2" class="text-blue-600 underline underline-offset-2 hover:text-blue-700" target="_blank" rel="noopener noreferrer">$1</a>',
        $esc
    );
    // inline code
    $esc = preg_replace(
        '/`([^`]+)`/',
        '<code class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-[0.8rem] font-mono text-gray-800">$1</code>',
        $esc
    );
    // bold **
    $esc = preg_replace('/\*\*(.+?)\*\*/', '<strong class="font-semibold text-gray-900">$1</strong>', $esc);
    // italic *
    $esc = preg_replace('/\*(.+?)\*/', '<em class="italic">$1</em>', $esc);
    return $esc;
}

function render_blog_content(string $content, array &$toc): string
{
    $lines      = preg_split("/\r?\n/", $content) ?: [];
    $inCodeBlock = false;
    $codeLang    = '';
    $inList      = false;
    $listTag     = 'ul';
    $htmlLines   = [];
    $usedIds     = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // ``` code blocks
        if (preg_match('/^```(.*)$/', $trimmed, $m)) {
            if (!$inCodeBlock) {
                $inCodeBlock = true;
                $codeLang    = trim($m[1] ?? '');
                $langClass   = $codeLang !== '' ? ' language-' . htmlspecialchars($codeLang, ENT_QUOTES, 'UTF-8') : '';
                if ($inList) { $htmlLines[] = '</' . $listTag . '>'; $inList = false; }
                $htmlLines[] = '<pre class="mt-4 mb-4 rounded-xl bg-gray-950 border border-gray-800 p-4 overflow-x-auto text-xs leading-snug text-gray-100 shadow-md"><code class="font-mono' . $langClass . '">';
            } else {
                $inCodeBlock = false;
                $codeLang    = '';
                $htmlLines[] = '</code></pre>';
            }
            continue;
        }

        if ($inCodeBlock) {
            $htmlLines[] = htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . "\n";
            continue;
        }

        // @[video](url) — responsive embed
        if (preg_match('/^@\[video\]\(([^)]+)\)$/', $trimmed, $m)) {
            if ($inList) { $htmlLines[] = '</' . $listTag . '>'; $inList = false; }
            $embedUrl = parse_video_url(trim($m[1]));
            if ($embedUrl !== '') {
                $safeUrl     = htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8');
                $htmlLines[] = '<div class="relative my-6 pb-[56.25%] h-0 overflow-hidden rounded-xl border border-gray-200 shadow-md">'
                    . '<iframe class="absolute inset-0 w-full h-full rounded-xl" src="' . $safeUrl . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                    . '</div>';
            }
            continue;
        }

        // --- horizontal rule
        if ($trimmed === '---') {
            if ($inList) { $htmlLines[] = '</' . $listTag . '>'; $inList = false; }
            $htmlLines[] = '<hr class="my-6 border-gray-200">';
            continue;
        }

        // > blockquote
        if (strncmp($trimmed, '> ', 2) === 0) {
            if ($inList) { $htmlLines[] = '</' . $listTag . '>'; $inList = false; }
            $inner       = apply_inline(htmlspecialchars(substr($trimmed, 2), ENT_QUOTES, 'UTF-8'));
            $htmlLines[] = '<blockquote class="my-4 border-l-4 border-blue-400 pl-4 text-gray-600 italic text-sm bg-blue-50/50 rounded-r-md py-1">' . $inner . '</blockquote>';
            continue;
        }

        // Lists
        $isBullet   = preg_match('/^[-*]\s+(.+)$/', $trimmed, $mBullet) === 1;
        $isNumbered = preg_match('/^\d+\.\s+(.+)$/', $trimmed, $mNum) === 1;

        if ($isBullet || $isNumbered) {
            $currentTag  = $isNumbered ? 'ol' : 'ul';
            $itemTextRaw = $isNumbered ? ($mNum[1] ?? '') : ($mBullet[1] ?? '');
            $itemEsc     = apply_inline(htmlspecialchars($itemTextRaw, ENT_QUOTES, 'UTF-8'));

            if (!$inList || $listTag !== $currentTag) {
                if ($inList) { $htmlLines[] = '</' . $listTag . '>'; }
                $listTag     = $currentTag;
                $htmlLines[] = $listTag === 'ol'
                    ? '<ol class="list-decimal list-outside ml-5 my-4 space-y-1.5 text-sm text-gray-700">'
                    : '<ul class="list-disc list-outside ml-5 my-4 space-y-1.5 text-sm text-gray-700">';
                $inList = true;
            }
            $htmlLines[] = '<li>' . $itemEsc . '</li>';
            continue;
        }

        if ($trimmed === '') {
            if ($inList) { $htmlLines[] = '</' . $listTag . '>'; $inList = false; }
            continue;
        }

        if ($inList) { $htmlLines[] = '</' . $listTag . '>'; $inList = false; }

        $esc = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');

        // Headings with TOC anchors (## and ###)
        if (strpos($esc, '## ') === 0 || strpos($esc, '### ') === 0) {
            $level        = strpos($esc, '### ') === 0 ? 3 : 2;
            $prefixLen    = $level === 3 ? 4 : 3;
            $text         = trim(substr($esc, $prefixLen));
            $baseId       = strtolower(preg_replace('/[^a-z0-9]+/i', '-', html_entity_decode($text, ENT_QUOTES, 'UTF-8')));
            $baseId       = trim($baseId, '-') ?: 'sezione';
            $id           = $baseId;
            $suffix       = 2;
            while (in_array($id, $usedIds, true)) { $id = $baseId . '-' . $suffix++; }
            $usedIds[]    = $id;
            $toc[]        = ['id' => $id, 'text' => html_entity_decode($text, ENT_QUOTES, 'UTF-8'), 'level' => $level];

            $classes = $level === 3
                ? 'text-sm sm:text-base font-semibold text-gray-800 mt-6 mb-2 scroll-mt-20'
                : 'text-lg sm:text-xl font-semibold text-gray-900 mt-8 mb-2 scroll-mt-20';
            $htmlLines[] = '<h' . $level . ' id="' . $id . '" class="' . $classes . '">' . $text . '</h' . $level . '>';
            continue;
        }

        // Regular paragraph line
        $htmlLines[] = '<p class="text-sm sm:text-base text-gray-700 leading-relaxed text-justify mb-4">' . apply_inline($esc) . '</p>';
    }

    if ($inList) { $htmlLines[] = '</' . $listTag . '>'; }

    return implode("\n", $htmlLines);
}

$toc             = [];
$renderedContent = render_blog_content($post['content'] ?? '', $toc);

// Reading time
$wordCount   = str_word_count(strip_tags($post['content'] ?? ''));
$readingMins = max(1, (int)ceil($wordCount / 220));

// Dates
$publishedDate = $post['published_at'] ?? $post['created_at'];
$updatedDate   = $post['updated_at'] ?? null;

/** @var array|null $course */
/** @var array|null $subject */
/** @var array $relatedPosts */

require __DIR__ . '/../partials/header_public.php';
?>

<!-- Reading progress bar -->
<div id="readingProgress" class="fixed top-[57px] left-0 h-0.5 bg-blue-600 z-40 w-0 transition-none pointer-events-none" aria-hidden="true"></div>

<div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 pt-20 pb-10 lg:flex-row lg:items-start">

    <!-- Sidebar sinistra: ToC + correlati -->
    <aside class="order-2 mt-4 w-full space-y-4 lg:order-1 lg:mt-0 lg:w-[260px] lg:shrink-0 lg:sticky lg:top-24">
        <?php if (!empty($toc)): ?>
            <section class="rounded-2xl border border-gray-200 bg-white px-4 py-4 text-sm shadow-sm">
                <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Contenuti</p>
                <ul class="space-y-1 text-[13px]">
                    <?php foreach ($toc as $entry): ?>
                        <li class="<?= $entry['level'] === 3 ? 'pl-3 text-gray-500' : 'text-gray-700' ?>">
                            <a href="#<?= htmlspecialchars($entry['id'], ENT_QUOTES, 'UTF-8') ?>"
                               class="inline-flex items-center gap-1 hover:text-blue-600 transition-colors">
                                <?php if ($entry['level'] === 3): ?><span class="h-1 w-1 rounded-full bg-gray-300 shrink-0"></span><?php endif; ?>
                                <span><?= htmlspecialchars($entry['text'], ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <section class="rounded-2xl border border-gray-200 bg-white px-4 py-4 text-sm shadow-sm">
            <h2 class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Articoli correlati</h2>
            <?php if (!empty($relatedPosts)): ?>
                <ul class="space-y-2.5 text-[13px]">
                    <?php foreach ($relatedPosts as $rel): ?>
                        <li>
                            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rel['slug']) ?>"
                               class="block text-gray-700 hover:text-blue-600 transition-colors">
                                <span class="block font-medium leading-snug"><?= htmlspecialchars($rel['title']) ?></span>
                                <span class="text-[11px] text-gray-400">
                                    <?php $d = $rel['published_at'] ?? $rel['created_at']; echo date('d/m/Y', strtotime($d)); ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-[12px] text-gray-400">Nessun articolo correlato.</p>
            <?php endif; ?>
        </section>
    </aside>

    <!-- Colonna centrale: articolo -->
    <div class="order-1 w-full min-w-0 lg:order-2 lg:flex-1">
        <div class="mx-auto w-full max-w-[760px]">

            <!-- Breadcrumb -->
            <nav aria-label="Breadcrumb" class="mb-4 flex items-center gap-1.5 text-[11px] text-gray-400">
                <a href="<?= BASE_URL ?>/" class="hover:text-gray-700 transition-colors">Home</a>
                <span>/</span>
                <a href="<?= BASE_URL ?>/blog" class="hover:text-gray-700 transition-colors">Blog</a>
                <?php if (!empty($course)): ?>
                    <span>/</span>
                    <a href="<?= BASE_URL ?>/blog?course=<?= urlencode($course['slug']) ?>"
                       class="hover:text-gray-700 transition-colors"><?= htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') ?></a>
                <?php endif; ?>
                <?php if (!empty($subject)): ?>
                    <span>/</span>
                    <span class="text-gray-600"><?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </nav>

            <a href="<?= BASE_URL ?>/blog" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 transition-colors mb-4">
                ← Torna al blog
            </a>

            <article id="articleBody">
                <header class="space-y-3 mb-6">
                    <?php if (!empty($course)): ?>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-100 px-3 py-0.5 text-[11px] font-medium text-blue-700">
                                <?= htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php if (!empty($subject)): ?>
                                <span class="text-gray-300">·</span>
                                <span class="text-[11px] text-gray-500"><?= htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">
                        <?= htmlspecialchars($post['title'] ?? '') ?>
                    </h1>

                    <div class="flex flex-wrap items-center gap-3 text-[12px] text-gray-400">
                        <time datetime="<?= htmlspecialchars(date('Y-m-d', strtotime($publishedDate))) ?>">
                            <?= date('d M Y', strtotime($publishedDate)) ?>
                        </time>
                        <span class="text-gray-300">·</span>
                        <span><?= $readingMins ?> min di lettura</span>
                        <?php if ($updatedDate && $updatedDate !== $publishedDate): ?>
                            <span class="text-gray-300">·</span>
                            <span>Aggiornato il <time datetime="<?= htmlspecialchars(date('Y-m-d', strtotime($updatedDate))) ?>"><?= date('d/m/Y', strtotime($updatedDate)) ?></time></span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (!empty($featuredImage) && !empty($featuredImage['path'])): ?>
                    <figure class="mb-6">
                        <img src="<?= BASE_URL . '/' . htmlspecialchars($featuredImage['path'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($featuredImage['alt_text'] ?? $post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                             class="w-full max-h-[420px] object-cover rounded-2xl border border-gray-200 shadow-md"
                             loading="eager" decoding="async">
                        <?php if (!empty($featuredImage['alt_text'])): ?>
                            <figcaption class="mt-2 text-center text-[11px] text-gray-400">
                                <?= htmlspecialchars($featuredImage['alt_text'], ENT_QUOTES, 'UTF-8') ?>
                            </figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endif; ?>

                <?php if (!empty($post['excerpt'])): ?>
                    <p class="mb-6 text-base text-gray-600 leading-relaxed border-l-4 border-blue-400 pl-4 italic bg-blue-50/50 rounded-r-md py-1">
                        <?= nl2br(htmlspecialchars($post['excerpt'], ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                <?php endif; ?>

                <div id="blogContentPublic">
                    <?= $renderedContent ?>
                </div>

                <!-- Share bar -->
                <?php
                $shareUrl    = $canonicalUrl;
                $shareTitle  = urlencode($post['title'] ?? '');
                $shareUrlEnc = urlencode($shareUrl);
                $shareLinks  = [
                    'x'        => ['url' => "https://twitter.com/intent/tweet?text={$shareTitle}&url={$shareUrlEnc}", 'label' => 'X', 'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.261 5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
                    'linkedin' => ['url' => "https://www.linkedin.com/sharing/share-offsite/?url={$shareUrlEnc}", 'label' => 'LinkedIn', 'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'],
                    'whatsapp' => ['url' => "https://wa.me/?text={$shareTitle}%20{$shareUrlEnc}", 'label' => 'WhatsApp', 'icon' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>'],
                    'telegram' => ['url' => "https://t.me/share/url?url={$shareUrlEnc}&text={$shareTitle}", 'label' => 'Telegram', 'icon' => '<path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>'],
                ];
                ?>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-3">Condividi l'articolo</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <?php foreach ($shareLinks as $key => $link): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>"
                               target="_blank" rel="noopener noreferrer"
                               title="Condividi su <?= $link['label'] ?>"
                               class="inline-flex items-center gap-1.5 rounded-full border border-gray-300 px-3 py-1.5 text-[12px] text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors">
                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <?= $link['icon'] ?>
                                </svg>
                                <?= $link['label'] ?>
                            </a>
                        <?php endforeach; ?>

                        <?php if (!empty($ogImage)): ?>
                        <!-- Instagram Stories (usa la copertina come anteprima) -->
                        <button id="instagramShareBtn"
                                data-cover-url="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>"
                                data-share-title="<?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                data-share-url="<?= htmlspecialchars($shareUrl, ENT_QUOTES, 'UTF-8') ?>"
                                title="Condividi nelle storie Instagram"
                                class="inline-flex items-center gap-1.5 rounded-full border border-gray-300 px-3 py-1.5 text-[12px] text-gray-600 hover:border-pink-500 hover:text-pink-600 transition-colors">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0C8.74 0 8.333.014 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.014 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.014 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.014-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.645-1.44-1.44 0-.795.645-1.44 1.44-1.44.795 0 1.44.645 1.44 1.44z"/>
                            </svg>
                            Storie Instagram
                        </button>
                        <?php endif; ?>

                        <!-- Copy link -->
                        <button id="copyLinkBtn"
                                title="Copia link"
                                class="inline-flex items-center gap-1.5 rounded-full border border-gray-300 px-3 py-1.5 text-[12px] text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                            </svg>
                            Copia link
                        </button>

                        <!-- Native share (mobile) -->
                        <button id="nativeShareBtn"
                                class="hidden items-center gap-1.5 rounded-full border border-gray-300 px-3 py-1.5 text-[12px] text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/>
                            </svg>
                            Condividi
                        </button>
                    </div>

                    <?php if (!empty($ogImage)): ?>
                    <!-- Instagram Stories: QR per continuare da smartphone (le storie non si pubblicano da desktop) -->
                    <div id="igQrModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
                        <div class="relative w-full max-w-xs rounded-2xl bg-white p-6 text-center shadow-xl">
                            <button id="igQrClose" type="button" aria-label="Chiudi"
                                    class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M6 6l12 12M18 6L6 18"/>
                                </svg>
                            </button>
                            <p class="mb-1 text-sm font-semibold text-gray-900">Condividi nelle storie Instagram</p>
                            <p class="mb-4 text-xs text-gray-500">Le storie si pubblicano solo da smartphone. Inquadra il codice con la fotocamera per aprire l'articolo sul telefono.</p>
                            <div id="igQrCode" class="mx-auto flex h-[176px] w-[176px] items-center justify-center"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Author box -->
                <section class="mt-10 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4 text-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm">
                            <span class="text-lg font-bold">G</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Autore</p>
                            <p class="text-sm font-semibold text-gray-900">Giorgio Di Fusco</p>
                            <p class="text-xs text-gray-500">Tutor e docente privato · Matematica, Informatica, Basi di Dati</p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <a href="https://www.linkedin.com/in/giorgio-di-fusco" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center rounded-full border border-gray-300 bg-white px-3 py-1 text-[11px] text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors">
                                LinkedIn
                            </a>
                            <a href="https://github.com/giorgiodifusco" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center rounded-full border border-gray-300 bg-white px-3 py-1 text-[11px] text-gray-600 hover:border-blue-500 hover:text-blue-600 transition-colors">
                                GitHub
                            </a>
                        </div>
                    </div>
                </section>
            </article>

        </div>
    </div>
</div>

<!-- JSON-LD structured data -->
<script type="application/ld+json">
<?= json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'Article',
    'headline'        => $post['title'] ?? '',
    'description'     => $metaDescription ?? '',
    'image'           => $ogImage ?? BASE_URL . '/public/assets/img/favicon.png',
    'datePublished'   => date('c', strtotime($publishedDate)),
    'dateModified'    => date('c', strtotime($updatedDate ?? $publishedDate)),
    'author'          => [
        '@type' => 'Person',
        'name'  => 'Giorgio Di Fusco',
        'url'   => BASE_URL,
    ],
    'publisher'       => [
        '@type' => 'Person',
        'name'  => 'Giorgio Di Fusco',
        'url'   => BASE_URL,
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $canonicalUrl,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<!-- BreadcrumbList JSON-LD -->
<script type="application/ld+json">
<?= json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => array_values(array_filter([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',  'item' => BASE_URL . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog',  'item' => BASE_URL . '/blog'],
        !empty($course)  ? ['@type' => 'ListItem', 'position' => 3, 'name' => $course['name'],  'item' => BASE_URL . '/blog?course=' . urlencode($course['slug'])] : null,
        !empty($subject) ? ['@type' => 'ListItem', 'position' => 4, 'name' => $subject['name'], 'item' => BASE_URL . '/blog?course=' . urlencode($course['slug'] ?? '') . '&subject=' . urlencode($subject['slug'])] : null,
    ])),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<!-- KaTeX + Highlight.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"
        onload="renderMathInElement(document.getElementById('blogContentPublic'),{delimiters:[{left:'$$',right:'$$',display:true},{left:'$',right:'$',display:false},{left:'\\\\(',right:'\\\\)',display:false},{left:'\\\\[',right:'\\\\]',display:true}]})"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css">
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"
        onload="document.getElementById('blogContentPublic').querySelectorAll('pre code').forEach(el=>hljs.highlightElement(el))"></script>

<?php if (!empty($ogImage)): ?>
<!-- QR per il fallback desktop del pulsante "Storie Instagram" -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Reading progress bar
    const article  = document.getElementById('articleBody');
    const progress = document.getElementById('readingProgress');
    if (article && progress) {
        window.addEventListener('scroll', function () {
            const scrolled = Math.max(0, -article.getBoundingClientRect().top);
            progress.style.width = Math.min(100, (scrolled / article.offsetHeight) * 100) + '%';
        }, { passive: true });
    }

    // Copy link
    const copyBtn = document.getElementById('copyLinkBtn');
    if (copyBtn && navigator.clipboard) {
        copyBtn.addEventListener('click', function () {
            navigator.clipboard.writeText(window.location.href).then(function () {
                const orig = copyBtn.innerHTML;
                copyBtn.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copiato!';
                copyBtn.classList.add('border-emerald-500', 'text-emerald-600');
                setTimeout(function () {
                    copyBtn.innerHTML = orig;
                    copyBtn.classList.remove('border-emerald-500', 'text-emerald-600');
                }, 2500);
            });
        });
    }

    // Native share (shown only when API is available — typically mobile)
    const nativeBtn = document.getElementById('nativeShareBtn');
    if (nativeBtn && navigator.share) {
        nativeBtn.classList.remove('hidden');
        nativeBtn.classList.add('inline-flex');
        nativeBtn.addEventListener('click', function () {
            navigator.share({ title: document.title, url: window.location.href }).catch(() => {});
        });
    }

    // Instagram Stories: le storie si pubblicano solo dall'app mobile, quindi
    // il flusso cambia in base al dispositivo:
    //  - su mobile con supporto alla condivisione di file, l'immagine viene
    //    passata direttamente al selettore nativo (un tap, niente download:
    //    la copertina resta in memoria, l'utente sceglie Instagram > Storie);
    //  - su desktop (dove Instagram non permette comunque di pubblicare
    //    storie) mostriamo un QR da inquadrare per continuare da telefono.
    const igBtn      = document.getElementById('instagramShareBtn');
    const igQrModal  = document.getElementById('igQrModal');
    const igQrCodeEl = document.getElementById('igQrCode');
    let igQrRendered = false;

    function openIgQrModal(url) {
        if (!igQrModal || !igQrCodeEl) return;
        if (!igQrRendered && window.QRCode) {
            igQrCodeEl.innerHTML = '';
            new QRCode(igQrCodeEl, {
                text: url, width: 176, height: 176,
                colorDark: '#111827', colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
            igQrRendered = true;
        }
        igQrModal.classList.remove('hidden');
        igQrModal.classList.add('flex');
    }
    function closeIgQrModal() {
        igQrModal?.classList.add('hidden');
        igQrModal?.classList.remove('flex');
    }
    document.getElementById('igQrClose')?.addEventListener('click', closeIgQrModal);
    igQrModal?.addEventListener('click', function (e) {
        if (e.target === igQrModal) closeIgQrModal();
    });

    if (igBtn) {
        igBtn.addEventListener('click', async function () {
            const coverUrl   = igBtn.dataset.coverUrl;
            const shareTitle = igBtn.dataset.shareTitle || document.title;
            const shareUrl   = igBtn.dataset.shareUrl || window.location.href;
            if (!coverUrl) return;

            if (navigator.canShare) {
                try {
                    const resp = await fetch(coverUrl);
                    if (resp.ok) {
                        const blob = await resp.blob();
                        const extMatch = coverUrl.split(/[?#]/)[0].match(/\.([a-zA-Z0-9]+)$/);
                        const ext = extMatch && ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extMatch[1].toLowerCase())
                            ? extMatch[1].toLowerCase() : 'jpg';
                        const file = new File([blob], `copertina.${ext}`, { type: blob.type || 'image/jpeg' });

                        if (navigator.canShare({ files: [file] })) {
                            try {
                                await navigator.share({ files: [file], title: shareTitle, text: shareTitle, url: shareUrl });
                            } catch (shareErr) {
                                // Utente ha annullato la condivisione: nessuna azione ulteriore.
                            }
                            return;
                        }
                    }
                } catch (e) {
                    // Fetch della copertina fallito: si passa comunque al QR sotto.
                }
            }

            openIgQrModal(shareUrl);
        });
    }
});
</script>

<?php require __DIR__ . '/../partials/footer_public.php'; ?>
