<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Pagamenti</h1>
        <p class="text-sm text-slate-400">Storico dei pagamenti registrati dal docente.</p>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 space-y-4">
        <p class="text-sm text-slate-400">
            Riepilogo dei pagamenti che il docente ha registrato per te.
        </p>

        <div class="space-y-3 mt-6" id="studentPaymentsList">
            <!-- Popolato via JS -->
        </div>
    </div>
</div>

<script src="<?= asset('assets/js/student_payments.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>