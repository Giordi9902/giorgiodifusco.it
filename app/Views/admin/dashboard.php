<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Panoramica Amministratore</h1>
            <p class="text-sm text-slate-400">Le informazioni principali sul tuo business in tempo reale.</p>
        </div>
    </div>

    <!-- Insights Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        <!-- Totale Studenti -->
        <div
            class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 flex flex-col items-center text-center">
            <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-full mb-4 ring-1 ring-indigo-500/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-100"><?= htmlspecialchars($stats['students_count']) ?></h2>
            <p class="text-sm font-medium text-slate-400 mt-1">Studenti iscritti</p>
        </div>

        <!-- Lezioni Programmate -->
        <div
            class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 flex flex-col items-center text-center">
            <div class="p-3 bg-amber-500/10 text-amber-400 rounded-full mb-4 ring-1 ring-amber-500/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-100"><?= htmlspecialchars($stats['lessons_scheduled']) ?></h2>
            <p class="text-sm font-medium text-slate-400 mt-1">Lezioni programmate</p>
        </div>

        <!-- Incassi Totali -->
        <div
            class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 flex flex-col items-center text-center">
            <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-full mb-4 ring-1 ring-emerald-500/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-100">
                €<?= number_format($stats['payments_total_amount'], 2, ',', '.') ?></h2>
            <p class="text-sm font-medium text-slate-400 mt-1">Stima incassi globali</p>
        </div>

        <!-- Saldo Pendente Totale -->
        <div
            class="bg-slate-900/80 rounded-2xl shadow-xl shadow-red-900/10 border border-slate-800 p-6 flex flex-col items-center text-center">
            <div class="p-3 bg-red-500/10 text-red-500 rounded-full mb-4 ring-1 ring-red-500/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-red-400">
                €<?= number_format($stats['pending_balance'] ?? 0, 2, ',', '.') ?></h2>
            <p class="text-sm font-medium text-slate-400 mt-1">Saldo pendente totale</p>
        </div>

        <!-- Minuti utilizzati -->
        <div
            class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 flex flex-col items-center text-center">
            <div class="p-3 bg-purple-500/10 text-purple-400 rounded-full mb-4 ring-1 ring-purple-500/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-100"><?= round($stats['packages_used_minutes'] / 60, 1) ?>h</h2>
            <p class="text-sm font-medium text-slate-400 mt-1">Ore pacchetti completate</p>
            <p class="text-xs text-slate-500 mt-1 font-mono">Su un totale di
                <?= round($stats['packages_total_minutes'] / 60, 1) ?>h vendute
            </p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <h2 class="text-lg font-semibold text-slate-100 mb-4 mt-8">Azioni rapide</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?= BASE_URL ?>/admin/students"
                class="border border-slate-700 border-dashed rounded-xl p-4 flex flex-col items-center justify-center text-indigo-400 hover:bg-slate-800 transition cursor-pointer text-center group">
                <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span class="text-sm font-medium">Gestisci Studenti</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/lessons"
                class="border border-slate-700 border-dashed rounded-xl p-4 flex flex-col items-center justify-center text-emerald-400 hover:bg-slate-800 transition cursor-pointer text-center group">
                <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                    </path>
                </svg>
                <span class="text-sm font-medium">Programma Lezioni</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/payments"
                class="border border-slate-700 border-dashed rounded-xl p-4 flex flex-col items-center justify-center text-amber-400 hover:bg-slate-800 transition cursor-pointer text-center group">
                <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="text-sm font-medium">Nuovo Pagamento</span>
            </a>
            <a href="<?= BASE_URL ?>/cms"
                class="border border-slate-700 border-dashed rounded-xl p-4 flex flex-col items-center justify-center text-purple-400 hover:bg-slate-800 hover:border-purple-500/30 transition cursor-pointer text-center group">
                <svg class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                <span class="text-sm font-medium">CMS Blog</span>
                <span class="text-[10px] text-slate-500 mt-0.5">Area articoli →</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>