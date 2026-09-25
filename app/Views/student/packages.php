<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Pacchetti lezioni</h1>
        <p class="text-sm text-slate-400">Storico dei pacchetti concordati e relativo utilizzo.</p>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 space-y-4">
        <p class="text-sm text-slate-400">
            Elenco dei pacchetti di ore acquistati, con minuti utilizzati e rimanenti.
        </p>

        <div class="space-y-3 mt-6" id="studentPackagesList">
            <!-- Popolato via JS -->
        </div>
    </div>
</div>

<script src="<?= asset('assets/js/student_packages.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>