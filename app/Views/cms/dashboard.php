<?php ob_start(); $layoutArea = 'cms'; ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">CMS Blog</h1>
            <p class="text-sm text-slate-400">Gestione e pubblicazione degli articoli del blog pubblico.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/blog" target="_blank"
                class="text-sm font-medium text-purple-400 hover:text-purple-300 transition">Vedi blog pubblico ↗</a>
            <a href="<?= BASE_URL ?>/cms/blog/new"
                class="inline-flex items-center justify-center rounded-full bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-purple-900/20 hover:bg-purple-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-300 transition border border-purple-500">
                + Nuovo articolo
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-5 flex flex-col items-center text-center shadow-xl shadow-purple-900/5">
            <div class="p-3 bg-purple-500/10 text-purple-400 rounded-full mb-3 ring-1 ring-purple-500/30">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-100"><?= (int)($stats['total'] ?? 0) ?></h2>
            <p class="text-sm text-slate-400 mt-1">Articoli totali</p>
        </div>

        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-5 flex flex-col items-center text-center shadow-xl shadow-emerald-900/5">
            <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-full mb-3 ring-1 ring-emerald-500/30">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-emerald-400"><?= (int)($stats['published'] ?? 0) ?></h2>
            <p class="text-sm text-slate-400 mt-1">Pubblicati</p>
        </div>

        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-5 flex flex-col items-center text-center shadow-xl shadow-amber-900/5">
            <div class="p-3 bg-amber-500/10 text-amber-400 rounded-full mb-3 ring-1 ring-amber-500/30">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-amber-400"><?= (int)($stats['drafts'] ?? 0) ?></h2>
            <p class="text-sm text-slate-400 mt-1">Bozze</p>
        </div>
    </div>

    <!-- Recent articles -->
    <div class="bg-slate-900/80 rounded-2xl border border-slate-800 shadow-xl">
        <div class="border-b border-slate-800 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-100">Articoli recenti</h2>
                <p class="text-xs text-slate-400 mt-0.5">Gli ultimi articoli creati o modificati.</p>
            </div>
            <a href="<?= BASE_URL ?>/cms/blog"
                class="text-xs font-medium text-purple-400 hover:text-purple-300 transition">Vedi tutti →</a>
        </div>

        <ul class="divide-y divide-slate-800">
            <?php if (empty($stats['recent'])): ?>
                <li class="px-6 py-8 text-center text-sm text-slate-500">
                    Nessun articolo ancora. <a href="<?= BASE_URL ?>/cms/blog/new" class="text-purple-400 hover:underline">Crea il primo →</a>
                </li>
            <?php else: ?>
                <?php foreach ($stats['recent'] as $post): ?>
                    <?php
                    $date = $post['published_at'] ?? $post['created_at'];
                    $dateStr = $date ? date('d/m/Y', strtotime($date)) : '';
                    $isPublished = $post['status'] === 'published';
                    ?>
                    <li class="px-6 py-4 flex items-center justify-between gap-4 hover:bg-slate-800/30 transition">
                        <div class="flex flex-col min-w-0">
                            <span class="text-sm font-medium text-slate-100 truncate"><?= htmlspecialchars($post['title']) ?></span>
                            <span class="text-xs text-slate-500 mt-0.5">
                                <?= $isPublished ? 'Pubblicato' : 'Bozza' ?> · <?= $dateStr ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium <?= $isPublished ? 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20' : 'bg-slate-700/50 text-slate-400 ring-1 ring-slate-600/30' ?>">
                                <?= $isPublished ? 'Pubblicato' : 'Bozza' ?>
                            </span>
                            <?php if ($isPublished): ?>
                                <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank"
                                    class="text-xs text-slate-500 hover:text-slate-300 transition">Vedi ↗</a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/cms/blog/edit/<?= (int)$post['id'] ?>"
                                class="text-xs font-medium text-purple-400 hover:text-purple-300 transition">Modifica</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Quick actions -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="<?= BASE_URL ?>/cms/blog/new"
            class="border border-slate-700 border-dashed rounded-xl p-5 flex items-center gap-4 text-purple-400 hover:bg-slate-800/40 hover:border-purple-500/30 transition group">
            <div class="p-2.5 bg-purple-500/10 rounded-lg ring-1 ring-purple-500/20 group-hover:bg-purple-500/15 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm">Scrivi un articolo</p>
                <p class="text-xs text-slate-500 mt-0.5">Crea un nuovo articolo con Markdown, immagini e video</p>
            </div>
        </a>

        <a href="<?= BASE_URL ?>/cms/blog"
            class="border border-slate-700 border-dashed rounded-xl p-5 flex items-center gap-4 text-slate-300 hover:bg-slate-800/40 hover:border-slate-600 transition group">
            <div class="p-2.5 bg-slate-800 rounded-lg ring-1 ring-slate-700 group-hover:bg-slate-700/60 transition">
                <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm">Gestisci articoli</p>
                <p class="text-xs text-slate-500 mt-0.5">Modifica, elimina o pubblica gli articoli esistenti</p>
            </div>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
