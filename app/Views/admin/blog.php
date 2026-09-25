<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Blog</h1>
            <p class="text-sm text-slate-400">Gestione degli articoli pubblici.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/blog" target="_blank"
                class="text-sm font-medium text-indigo-400 hover:text-indigo-300">Apri blog pubblico</a>
            <a href="<?= BASE_URL ?>/admin/blog/new"
                class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 transition border border-indigo-400">
                Nuovo articolo
            </a>
        </div>
    </div>

    <!-- Card blog -->
    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800">
        <div class="border-b border-slate-800 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-100">Tutti gli articoli</h2>
                <p class="text-xs text-slate-400">Elenco degli articoli salvati (bozze e pubblicati).</p>
            </div>
            <button type="button" onclick="loadBlogPosts()"
                class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-300 hover:bg-slate-700 transition">
                Aggiorna elenco
            </button>
        </div>

        <div class="p-6">
            <ul id="blogPostsList" class="space-y-3 text-sm text-slate-300">
                <!-- Popolato via JS -->
            </ul>
        </div>
    </div>
</div>

<script src="<?= asset('assets/js/admin_blog.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>