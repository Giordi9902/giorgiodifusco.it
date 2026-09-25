<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Materiali didattici</h1>
        <p class="text-sm text-slate-400">Carica e organizza il materiale per ogni studente.</p>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6">
        <p class="text-sm text-slate-400">
            Questa sezione permetterà di associare file e risorse a ciascuno studente, rendendoli disponibili nella
            loro area personale. Nel prossimo step collegheremo questa pagina al modello "materials".
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>