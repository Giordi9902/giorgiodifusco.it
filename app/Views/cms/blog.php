<?php ob_start(); $layoutArea = 'cms'; ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Articoli</h1>
            <p class="text-sm text-slate-400">Tutti gli articoli del blog (bozze e pubblicati).</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/blog" target="_blank"
                class="text-sm font-medium text-purple-400 hover:text-purple-300 transition">Blog pubblico ↗</a>
            <a href="<?= BASE_URL ?>/cms/blog/new"
                class="inline-flex items-center justify-center rounded-full bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-purple-900/20 hover:bg-purple-500 transition border border-purple-500">
                + Nuovo articolo
            </a>
        </div>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl border border-slate-800">
        <div class="border-b border-slate-800 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-100">Tutti gli articoli</h2>
                <p class="text-xs text-slate-400 mt-0.5">Elenco degli articoli salvati.</p>
            </div>
            <button type="button" onclick="loadBlogPosts()"
                class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-300 hover:bg-slate-700 transition">
                Aggiorna
            </button>
        </div>

        <div class="p-6">
            <ul id="blogPostsList" class="space-y-3 text-sm text-slate-300">
                <!-- Populated by JS -->
            </ul>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/assets/js/admin_blog.js?v=<?= filemtime(__DIR__ . '/../../public/assets/js/admin_blog.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
