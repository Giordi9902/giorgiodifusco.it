<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Le tue lezioni</h1>
        <p class="text-sm text-slate-400">Calendario delle lezioni già svolte e di quelle future.</p>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 space-y-4">
        <p class="text-sm text-slate-400">
            Qui trovi le lezioni programmate e quelle già svolte. Per ogni lezione potrai aprire il registro (quando
            presente) con il resoconto e gli eventuali compiti.
        </p>
        <div class="space-y-3 mt-6" id="studentLessonsList">
            <!-- Popolato via JS -->
        </div>

        <div id="studentLessonsPagination" class="mt-4 flex items-center justify-between text-xs text-slate-400 hidden">
            <button type="button" data-role="prev-page"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700/70 bg-slate-900/70 px-3 py-1.5 text-[11px] font-medium text-slate-200 hover:bg-slate-800 transition">
                <span>&larr;</span>
                <span>Pagina precedente</span>
            </button>
            <span data-role="page-info" class="text-[11px] text-slate-400"></span>
            <button type="button" data-role="next-page"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700/70 bg-slate-900/70 px-3 py-1.5 text-[11px] font-medium text-slate-200 hover:bg-slate-800 transition">
                <span>Pagina successiva</span>
                <span>&rarr;</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal Dettagli Lezione -->
<div id="lessonDetailsModal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex-col items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-lg shadow-2xl p-6 relative">
        <button onclick="closeLessonDetails()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-200">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="text-xl font-semibold text-slate-50 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Dettagli Lezione
        </h3>

        <div class="space-y-4">
            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Argomento</p>
                <p id="modalLessonTopic" class="text-base text-slate-200 font-medium"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Data e Ora</p>
                    <p id="modalLessonDate" class="text-sm text-slate-300"></p>
                </div>
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Durata</p>
                    <p id="modalLessonDuration" class="text-sm text-slate-300"></p>
                </div>
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Tipo</p>
                    <p id="modalLessonType" class="text-sm text-slate-300"></p>
                </div>
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Stato</p>
                    <p id="modalLessonStatus" class="text-sm text-slate-300"></p>
                </div>
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800 col-span-2">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-1">Modalità</p>
                    <p id="modalLessonMode" class="text-sm text-slate-300"></p>
                </div>
            </div>

            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800 mt-4">
                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-2">Registro / Note</p>
                <p class="text-sm text-slate-400 italic">Il registro di questa lezione sarà disponibile non appena
                    l'insegnante compilerà il resoconto.</p>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
            <a id="modalJoinButton" href="#" target="_blank"
                class="hidden px-5 py-2.5 rounded-xl bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-500 transition text-center">
                Entra in riunione
            </a>
            <button onclick="closeLessonDetails()"
                class="px-5 py-2.5 rounded-xl bg-slate-800 text-sm font-semibold text-slate-200 hover:bg-slate-700 transition">
                Chiudi
            </button>
        </div>
    </div>
</div>

<script src="<?= asset('assets/js/student_lessons.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>