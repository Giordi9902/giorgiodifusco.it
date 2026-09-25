<?php
$pageTitle       = 'Blog | GiorgioDiFusco.it';
$metaDescription = 'Articoli tecnici su matematica, informatica e basi di dati. Approfondimenti, esempi e casi d\'uso spiegati con linguaggio chiaro.';
$canonicalUrl    = BASE_URL . '/blog' . (isset($_GET['course']) ? '?course=' . urlencode($_GET['course']) : '');
$ogTitle         = 'Blog tecnico · Giorgio Di Fusco';
$ogDescription   = $metaDescription;

require __DIR__ . '/../partials/header_public.php';

function blog_page_url(int $p): string
{
    $params = $_GET;
    $params['page'] = $p;
    unset($params['url']);
    return BASE_URL . '/blog?' . http_build_query($params);
}

function blog_plain_excerpt(string $content, int $length = 180): string
{
    return mb_substr(\Core\BlogContent::plainText($content), 0, $length);
}
?>

<div class="pt-20 px-4 pb-16">
    <div class="max-w-6xl mx-auto">
        <header class="mb-10 pt-8">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 mb-2">Blog</p>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Articoli, esempi e appunti</h1>
            <p class="text-sm sm:text-base text-gray-600 max-w-2xl">
                Approfondimenti tecnici, snippet di codice e casi d'uso spiegati passo passo.
            </p>
        </header>

        <?php
        $courses           = $courses ?? [];
        $currentCourse     = $currentCourse ?? null;
        $subjectsForCourse = $subjectsForCourse ?? [];
        $currentSubject    = $currentSubject ?? null;
        $recentPosts       = $recentPosts ?? [];
        $search            = $search ?? null;
        $page              = $page ?? 1;
        $totalPages        = $totalPages ?? 1;
        $totalPosts        = $totalPosts ?? 0;
        ?>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,7fr)_minmax(260px,3fr)] items-start">

            <!-- Main column -->
            <section>
                <?php if ($search !== null): ?>
                    <p class="mb-5 text-sm text-gray-500">
                        <?= $totalPosts ?> risultat<?= $totalPosts === 1 ? 'o' : 'i' ?> per
                        <span class="text-gray-900 font-medium">"<?= htmlspecialchars($search) ?>"</span>
                        — <a href="<?= BASE_URL ?>/blog" class="text-blue-600 hover:text-blue-700 underline underline-offset-2">rimuovi filtro</a>
                    </p>
                <?php endif; ?>

                <?php if (!empty($posts)): ?>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($posts as $post): ?>
                            <?php
                            $date        = $post['published_at'] ?? $post['created_at'];
                            $wordCount   = \Core\BlogContent::wordCount($post['content'] ?? '');
                            $readingMins = max(1, (int)ceil($wordCount / 220));
                            ?>
                            <article class="group flex flex-col rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm transition hover:-translate-y-px hover:border-blue-200 hover:shadow-md hover:shadow-blue-50">

                                <?php if (!empty($post['featured_image_path'])): ?>
                                    <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" tabindex="-1" aria-hidden="true">
                                        <img src="<?= BASE_URL . '/' . htmlspecialchars($post['featured_image_path']) ?>"
                                            alt="<?= htmlspecialchars($post['featured_image_alt'] ?? $post['title']) ?>"
                                            class="w-full h-44 object-cover"
                                            loading="lazy" decoding="async">
                                    </a>
                                <?php else: ?>
                                    <div class="w-full h-2 bg-gradient-to-r from-blue-600 to-blue-400"></div>
                                <?php endif; ?>

                                <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="flex flex-1 flex-col p-5">
                                    <div class="mb-2.5 flex items-center justify-between gap-2">
                                        <?php if (!empty($post['subject_name']) || !empty($currentSubject['name'])): ?>
                                            <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-100 px-2.5 py-0.5 text-[10px] font-medium text-blue-700">
                                                <?= htmlspecialchars($post['subject_name'] ?? $currentSubject['name']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <time datetime="<?= date('Y-m-d', strtotime($date)) ?>"
                                            class="ml-auto text-[10px] uppercase tracking-[0.12em] text-gray-400 shrink-0">
                                            <?= date('d M Y', strtotime($date)) ?>
                                        </time>
                                    </div>

                                    <h2 class="mb-1 line-clamp-2 text-base font-semibold text-gray-900 group-hover:text-blue-700 leading-snug transition-colors">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </h2>

                                    <p class="text-[11px] text-gray-400 mb-3"><?= $readingMins ?> min di lettura</p>

                                    <?php if (!empty($post['excerpt'])): ?>
                                        <p class="mb-4 line-clamp-3 text-sm text-gray-600 leading-relaxed">
                                            <?= htmlspecialchars($post['excerpt']) ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="mb-4 line-clamp-3 text-sm text-gray-600 leading-relaxed">
                                            <?= htmlspecialchars(blog_plain_excerpt($post['content'] ?? '', 180)) ?>…
                                        </p>
                                    <?php endif; ?>

                                    <span class="mt-auto inline-flex items-center text-xs font-medium text-blue-600 group-hover:text-blue-700">
                                        Leggi l'articolo <span class="ml-1">→</span>
                                    </span>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-10 flex items-center justify-center gap-1" aria-label="Navigazione pagine">
                            <?php if ($page > 1): ?>
                                <a href="<?= blog_page_url($page - 1) ?>"
                                    class="inline-flex items-center justify-center rounded-full border border-gray-300 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 transition">
                                    ← Precedente
                                </a>
                            <?php endif; ?>

                            <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                                <a href="<?= blog_page_url($p) ?>"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border text-xs transition
                                          <?= $p === $page ? 'border-blue-600 bg-blue-600 text-white font-semibold' : 'border-gray-300 text-gray-600 hover:bg-gray-100' ?>">
                                    <?= $p ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="<?= blog_page_url($page + 1) ?>"
                                    class="inline-flex items-center justify-center rounded-full border border-gray-300 px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 transition">
                                    Successivo →
                                </a>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-sm text-gray-500 text-center">
                        <?php if ($search !== null): ?>
                            Nessun articolo trovato per "<?= htmlspecialchars($search) ?>". Prova un altro termine.
                        <?php else: ?>
                            Nessun articolo pubblicato per questa selezione. Prova a cambiare corso o materia.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Sidebar -->
            <aside class="space-y-4 lg:sticky lg:top-24">
                <!-- Search -->
                <section class="rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm">
                    <h2 class="mb-1 text-sm font-semibold text-gray-900">Cerca</h2>
                    <p class="mb-3 text-[11px] text-gray-500">Titolo, contenuto o argomento.</p>
                    <form action="<?= BASE_URL ?>/blog" method="get" role="search">
                        <?php if ($currentCourse): ?>
                            <input type="hidden" name="course" value="<?= htmlspecialchars($currentCourse['slug']) ?>">
                        <?php endif; ?>
                        <?php if ($currentSubject): ?>
                            <input type="hidden" name="subject" value="<?= htmlspecialchars($currentSubject['slug']) ?>">
                        <?php endif; ?>
                        <div class="flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition">
                            <svg class="h-3.5 w-3.5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                            </svg>
                            <input type="search" name="q"
                                class="w-full bg-transparent text-xs text-gray-900 placeholder:text-gray-400 focus:outline-none"
                                placeholder="es. normalizzazione, JOIN, SQL..."
                                value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <button type="submit"
                            class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition">
                            Cerca
                        </button>
                    </form>
                </section>

                <!-- Categorie / Corsi -->
                <?php if (!empty($courses)): ?>
                    <section class="rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm">
                        <h2 class="mb-3 text-sm font-semibold text-gray-900">Categorie</h2>
                        <div class="space-y-3 text-xs">
                            <div>
                                <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400">Corsi</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <a href="<?= BASE_URL ?>/blog"
                                        class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-medium transition
                                              <?= $currentCourse ? 'border-gray-300 text-gray-600 hover:bg-gray-100' : 'border-blue-600 bg-blue-50 text-blue-700' ?>">
                                        Tutti
                                    </a>
                                    <?php foreach ($courses as $course): ?>
                                        <?php $isActive = $currentCourse && $currentCourse['id'] == $course['id']; ?>
                                        <a href="<?= BASE_URL ?>/blog?course=<?= urlencode($course['slug']) ?>"
                                            class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-medium transition
                                                  <?= $isActive ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-600 hover:bg-gray-100' ?>">
                                            <?= htmlspecialchars($course['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if ($currentCourse && !empty($subjectsForCourse)): ?>
                                <div>
                                    <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400">
                                        Materie · <?= htmlspecialchars($currentCourse['name']) ?>
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <a href="<?= BASE_URL ?>/blog?course=<?= urlencode($currentCourse['slug']) ?>"
                                            class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-medium transition
                                                  <?= $currentSubject ? 'border-gray-300 text-gray-600 hover:bg-gray-100' : 'border-blue-600 bg-blue-50 text-blue-700' ?>">
                                            Tutte
                                        </a>
                                        <?php foreach ($subjectsForCourse as $subject): ?>
                                            <?php $isSubActive = $currentSubject && $currentSubject['id'] == $subject['id']; ?>
                                            <a href="<?= BASE_URL ?>/blog?course=<?= urlencode($currentCourse['slug']) ?>&subject=<?= urlencode($subject['slug']) ?>"
                                                class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-medium transition
                                                      <?= $isSubActive ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-600 hover:bg-gray-100' ?>">
                                                <?= htmlspecialchars($subject['name']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Articoli recenti -->
                <?php if (!empty($recentPosts)): ?>
                    <section class="rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm">
                        <h2 class="mb-3 text-sm font-semibold text-gray-900">Recenti</h2>
                        <ul class="space-y-3">
                            <?php foreach ($recentPosts as $rp): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rp['slug']) ?>"
                                        class="flex gap-3 group">
                                        <?php if (!empty($rp['featured_image_path'])): ?>
                                            <img src="<?= BASE_URL . '/' . htmlspecialchars($rp['featured_image_path']) ?>"
                                                alt="" class="h-12 w-12 rounded-lg object-cover shrink-0 border border-gray-200"
                                                loading="lazy" decoding="async" aria-hidden="true">
                                        <?php else: ?>
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 shrink-0 flex items-center justify-center border border-gray-200">
                                                <span class="text-xs text-gray-400">▤</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-medium text-gray-800 group-hover:text-blue-700 line-clamp-2 transition-colors leading-snug">
                                                <?= htmlspecialchars($rp['title']) ?>
                                            </p>
                                            <time datetime="<?= date('Y-m-d', strtotime($rp['published_at'] ?? $rp['created_at'])) ?>"
                                                class="text-[10px] text-gray-400">
                                                <?= date('d/m/Y', strtotime($rp['published_at'] ?? $rp['created_at'])) ?>
                                            </time>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>
            </aside>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer_public.php'; ?>