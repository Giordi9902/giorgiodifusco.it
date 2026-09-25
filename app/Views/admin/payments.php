<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Pagamenti</h1>
        <p class="text-sm text-slate-400">Registra e consulta i pagamenti degli studenti.</p>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6">
        <p class="text-sm text-slate-400">
            Qui potrai tenere traccia dei pagamenti ricevuti, note collegate e saldo per ciascuno studente. In un
            secondo momento verrà collegata alla tabella "payments".
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>