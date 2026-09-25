<?php ob_start(); ?>

<!-- Quill and KaTeX for WYSIWYG editor in Material tab -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" />
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="<?= BASE_URL ?>/admin/students"
                    class="text-indigo-400 hover:text-indigo-300 transition shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-slate-50"><?= htmlspecialchars($student['name']) ?></h1>
            </div>
            <p class="text-sm text-slate-400">Gestione avanzata studente: lezioni, registro, materiali e saldo.</p>
        </div>
    </div>

    <!-- Stats overview -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-slate-900/80 rounded-2xl shadow-xl border border-slate-800 p-4">
            <p class="text-xs text-slate-400 mb-1">Lezioni totali</p>
            <p class="text-2xl font-bold text-indigo-400"><?= $stats['lessons_total'] ?></p>
        </div>
        <div class="bg-slate-900/80 rounded-2xl shadow-xl border border-slate-800 p-4">
            <p class="text-xs text-slate-400 mb-1">Minuti residui</p>
            <p
                class="text-2xl font-bold <?= $stats['packages_remaining_minutes'] > 0 ? 'text-emerald-400' : 'text-amber-400' ?>">
                <?= $stats['packages_remaining_minutes'] ?>
            </p>
        </div>
        <div class="bg-slate-900/80 rounded-2xl shadow-xl border border-slate-800 p-4">
            <p class="text-xs text-slate-400 mb-1">Pacchetti attivi</p>
            <p class="text-2xl font-bold text-sky-400"><?= $stats['packages_active'] ?></p>
        </div>
        <div class="bg-slate-900/80 rounded-2xl shadow-xl border border-slate-800 p-4">
            <p class="text-xs text-slate-400 mb-1">Totale pagato</p>
            <p id="statTotalPaid" class="text-2xl font-bold text-slate-50">€
                <?= number_format($stats['payments_total_amount'], 2, ',', '.') ?>
            </p>
        </div>
        <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-red-900/10 border border-slate-800 p-4">
            <p class="text-xs text-slate-400 mb-1">Da saldare</p>
            <p id="statPendingBalance" class="text-2xl font-bold text-red-400">€
                <?= number_format($stats['pending_balance'] ?? 0, 2, ',', '.') ?>
            </p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-slate-800">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs" id="studentTabs">
            <button onclick="switchTab('lezioni')" id="tab-lezioni"
                class="tab-btn border-indigo-500 text-indigo-400 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                Lezioni
            </button>
            <button onclick="switchTab('registro')" id="tab-registro"
                class="tab-btn border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-500 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                Registro
            </button>
            <button onclick="switchTab('pacchetti')" id="tab-pacchetti"
                class="tab-btn border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-500 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                Pacchetti
            </button>
            <button onclick="switchTab('saldo')" id="tab-saldo"
                class="tab-btn border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-500 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                Saldo e Pagamenti
            </button>
            <button onclick="switchTab('materiale')" id="tab-materiale"
                class="tab-btn border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-500 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                Materiale
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 min-h-[400px]">

        <!-- LEZIONI TAB -->
        <div id="content-lezioni" class="tab-content block">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-50">Lezioni dello studente</h2>
                    <p class="text-sm text-slate-400">Calendario delle lezioni programmate e storico delle lezioni
                        passate.</p>
                </div>
                <button onclick="openModal('lessonModal')"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">Associa
                    Nuova Lezione</button>
            </div>

            <div class="space-y-8">
                <!-- Sezione Lezioni Future -->
                <div>
                    <h3 class="text-md font-semibold text-slate-200 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Prossime Lezioni
                    </h3>
                    <div id="futureLessonsList" class="space-y-3">
                        <p class="text-sm text-slate-400 mb-4">Caricamento lezioni in corso...</p>
                    </div>
                </div>

                <!-- Sezione Lezioni Passate (Storico) -->
                <div>
                    <h3
                        class="text-md font-semibold text-slate-200 mb-3 pt-6 border-t border-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Storico Lezioni
                    </h3>
                    <div id="pastLessonsList" class="space-y-3">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- REGISTRO TAB -->
        <div id="content-registro" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-slate-50">Registro note</h2>
                <div class="flex gap-2 w-1/2">
                    <input type="text" id="newNoteContent" placeholder="Aggiungi una nota veloce..."
                        class="flex-1 border border-slate-700 bg-slate-900 rounded-md p-2 text-sm text-slate-200">
                    <button onclick="saveNote(event)"
                        class="px-3 py-1.5 rounded-md bg-indigo-600 text-xs font-semibold text-white hover:bg-indigo-500 transition">Aggiungi</button>
                </div>
            </div>
            <div id="notesList" class="space-y-3">
                <p class="text-sm text-slate-400 mb-4">Caricamento note in corso...</p>
            </div>
        </div>

        <!-- PACCHETTI TAB -->
        <div id="content-pacchetti" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-slate-50">Pacchetti Ore</h2>
                <button onclick="openModal('packageModal')"
                    class="px-3 py-1.5 rounded-full bg-indigo-600 text-xs font-semibold text-white hover:bg-indigo-500 transition">Nuovo
                    Pacchetto</button>
            </div>
            <div id="packagesList" class="space-y-3">
                <p class="text-sm text-slate-400 mb-4">Caricamento pacchetti in corso...</p>
            </div>
        </div>

        <!-- SALDO TAB -->
        <div id="content-saldo" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-slate-50">Pagamenti</h2>
                <button onclick="openModal('paymentModal')"
                    class="px-3 py-1.5 rounded-full bg-emerald-600 text-xs font-semibold text-white hover:bg-emerald-500 transition">Registra
                    Pagamento</button>
            </div>
            <div id="paymentsList" class="space-y-3">
                <p class="text-sm text-slate-400 mb-4">Caricamento pagamenti in corso...</p>
            </div>
        </div>

        <!-- MATERIALE TAB -->
        <div id="content-materiale" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-slate-50">Condivisione Materiale</h2>
                <button onclick="saveTextMaterial()"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium rounded-lg transition shadow-lg shadow-indigo-500/20">Salva
                    Testo (Editor)</button>
            </div>

            <div class="space-y-6">
                <!-- WYSIWYG Editor -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Editor di Testo / Dispense (Supporta
                        LaTeX)</label>
                    <div class="border border-slate-700 rounded-lg overflow-hidden bg-white text-slate-900">
                        <div id="editor-container" class="h-64"></div>
                    </div>
                </div>

                <!-- File Upload & Link -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-slate-200 mb-3">Carica File</h3>
                            <input type="file" id="materialFile"
                                class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-500/10 file:text-indigo-400 hover:file:bg-indigo-500/20 cursor-pointer" />
                        </div>
                        <button onclick="saveFileMaterial()"
                            class="mt-3 w-full px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded-lg transition border border-slate-700">Carica
                            file</button>
                    </div>

                    <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-slate-200 mb-3">Condividi Link</h3>
                            <input type="url" id="materialLink" placeholder="https://..."
                                class="block w-full border border-slate-700 bg-slate-900 rounded-md p-2.5 text-sm text-slate-200 mb-3">
                        </div>
                        <button onclick="saveLinkMaterial()"
                            class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded-lg transition border border-slate-700">Aggiungi
                            Link</button>
                    </div>
                </div>

                <hr class="border-slate-800 my-4" />
                <h3 class="text-sm font-medium text-slate-200 mb-3">Materiale già caricato</h3>
                <div id="materialsList" class="space-y-3">
                    <p class="text-xs text-slate-400">Caricamento materiale in corso...</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const studentId = <?= $student['id'] ?>;
</script>
<script src="<?= asset('assets/js/student_detail.js') ?>"></script>

<!-- Toast container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- MODALS -->
<!-- Modal Lezione -->
<div id="lessonModal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flexitems-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl p-6 m-auto">
        <h3 class="text-xl font-semibold text-slate-50 mb-4">Associa Nuova Lezione</h3>
        <form id="lessonForm" onsubmit="submitLesson(event)" class="space-y-4">
            <input type="hidden" id="lessonPackageId" value="">
            <input type="hidden" id="lessonPackageRemaining" value="">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Data e Ora</label>
                <input type="datetime-local" id="lessonDateTime" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Durata (minuti)</label>
                <input type="number" id="lessonDuration" value="60" min="15" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Tipo lezione</label>
                <select id="lessonType"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
                    <option value="single">Lezione singola</option>
                    <option value="package">Inclusa in un pacchetto</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Modalità</label>
                <select id="lessonLocationType"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
                    <option value="in_person">In presenza</option>
                    <option value="online">Online</option>
                </select>
            </div>
            <div id="lessonMeetingLinkWrapper" class="hidden">
                <label class="block text-sm font-medium text-slate-300 mb-1">Link riunione (Discord, Meet, ecc.)</label>
                <input type="url" id="lessonMeetingLink"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200"
                    placeholder="https://...">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Argomento</label>
                <input type="text" id="lessonTopic" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200"
                    placeholder="Es. Ripasso derivate">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Prezzo (€)</label>
                    <input type="number" id="lessonPrice" step="0.01"
                        class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200"
                        placeholder="0.00">
                </div>
                <div class="flex items-center pt-6">
                    <input type="checkbox" id="lessonIsPaid"
                        class="w-4 h-4 text-indigo-600 bg-slate-950 border-slate-700 rounded focus:ring-indigo-500">
                    <label for="lessonIsPaid" class="ml-2 text-sm font-medium text-slate-300">Già pagata?</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal('lessonModal')"
                    class="px-4 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 transition">Annulla</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 transition">Salva</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pacchetto -->
<div id="packageModal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flexitems-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl p-6 m-auto">
        <h3 class="text-xl font-semibold text-slate-50 mb-4">Nuovo Pacchetto Ore</h3>
        <form id="packageForm" onsubmit="submitPackage(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Totale Ore (es. 10)</label>
                <input type="number" id="packageHours" value="10" min="1" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Prezzo (€)</label>
                <input type="number" id="packagePrice" step="0.01"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200"
                    placeholder="0.00">
            </div>
            <div class="flex items-center mb-2">
                <input type="checkbox" id="packageIsPaid"
                    class="w-4 h-4 text-indigo-600 bg-slate-950 border-slate-700 rounded focus:ring-indigo-500">
                <label for="packageIsPaid" class="ml-2 text-sm font-medium text-slate-300">Già pagato?</label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Stato</label>
                <select id="packageStatus"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
                    <option value="active">Attivo</option>
                    <option value="completed">Completato (Storico)</option>
                    <option value="cancelled">Annullato</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Data Inserimento</label>
                <input type="datetime-local" id="packageCreatedAt"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200"
                    title="Lascia vuoto per usare la data odierna">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Descrizione</label>
                <input type="text" id="packageDesc"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200"
                    placeholder="Es. Pacchetto 10 ore Matematica">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal('packageModal')"
                    class="px-4 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 transition">Annulla</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 transition">Salva</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pagamento -->
<div id="paymentModal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flexitems-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md shadow-2xl p-6 m-auto">
        <h3 class="text-xl font-semibold text-slate-50 mb-4">Registra Pagamento</h3>
        <form id="paymentForm" onsubmit="submitPayment(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Tipo pagamento</label>
                <select id="paymentType" onchange="updatePaymentTypeFields()"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
                    <option value="single">Lezione singola</option>
                    <option value="package">Pagamento pacchetto</option>
                </select>
            </div>
            <div id="paymentLessonWrapper">
                <label class="block text-sm font-medium text-slate-300 mb-1">Lezione da saldare (Opzionale)</label>
                <select id="paymentLessonId" onchange="prefillPaymentAmount()"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
                    <option value="">Nessuna lezione specifica</option>
                </select>
            </div>
            <div id="paymentPackageWrapper" class="hidden">
                <label class="block text-sm font-medium text-slate-300 mb-1">Pacchetto</label>
                <select id="paymentPackageId" onchange="prefillPaymentAmount()"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
                </select>
                <p id="paymentPackageEmpty" class="hidden text-xs text-amber-400 mt-1">Nessun pacchetto da saldare per
                    questo studente.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Importo (€)</label>
                <input type="number" id="paymentAmount" step="0.01" min="0.01" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200"
                    placeholder="0.00">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Data</label>
                <input type="date" id="paymentDate" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Metodo</label>
                <select id="paymentMethod"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
                    <option value="Bonifico">Bonifico</option>
                    <option value="Contanti">Contanti</option>
                    <option value="Satispay">Satispay</option>
                    <option value="Paypal">Paypal</option>
                    <option value="Altro">Altro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Note (Opzionale)</label>
                <input type="text" id="paymentNotes"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg p-2.5 text-slate-200">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal('paymentModal')"
                    class="px-4 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 transition">Annulla</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 transition">Registra</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal di conferma generica -->
<div id="confirmModal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-sm shadow-2xl p-5 m-auto">
        <h3 class="text-lg font-semibold text-slate-50 mb-3">Conferma azione</h3>
        <p id="confirmMessage" class="text-sm text-slate-300 mb-6"></p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="confirmModalCancel()"
                class="px-4 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 transition">Annulla</button>
            <button type="button" onclick="confirmModalConfirm()"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500 transition">Conferma</button>
        </div>
    </div>

</div>



<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>