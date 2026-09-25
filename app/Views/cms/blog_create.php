<?php ob_start(); $layoutArea = 'cms'; ?>

<div class="space-y-6">
    <div class="flex items-baseline justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Nuovo articolo</h1>
            <p class="text-sm text-slate-400">Scrivi con l'editor visuale: titoli, liste, link, immagini, codice, tabelle e video.</p>
        </div>
        <a href="<?= BASE_URL ?>/cms/blog"
            class="inline-flex items-center justify-center rounded-full border border-slate-700 px-3 py-1.5 text-sm font-medium text-slate-200 hover:bg-slate-800 transition">
            ← Lista articoli
        </a>
    </div>

    <?php $post = null; $featuredImage = null; require __DIR__ . '/_blog_form.php'; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
