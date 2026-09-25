<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Note del docente</h1>
        <p class="text-sm text-slate-400">Qui trovi le note e le osservazioni che il docente ha inserito nel tuo registro.</p>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 space-y-4">
        <p class="text-sm text-slate-400">
            Le note sono visibili solo a te e al docente e possono riguardare l'andamento del percorso, suggerimenti di studio o altre comunicazioni importanti.
        </p>

        <div class="space-y-3 mt-4" id="studentNotesList">
            <!-- Popolato via JS -->
        </div>
    </div>
</div>

<script src="<?= asset('assets/js/student_notes.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>