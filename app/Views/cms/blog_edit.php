<?php ob_start(); $layoutArea = 'cms'; ?>

<div class="space-y-6">
    <div class="flex items-baseline justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Modifica articolo</h1>
            <p class="text-sm text-slate-400">Aggiorna il contenuto con l'editor visuale.</p>
        </div>
        <a href="<?= BASE_URL ?>/cms/blog"
            class="inline-flex items-center justify-center rounded-full border border-slate-700 px-3 py-1.5 text-sm font-medium text-slate-200 hover:bg-slate-800 transition">
            ← Lista articoli
        </a>
    </div>

    <?php require __DIR__ . '/_blog_form.php'; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
