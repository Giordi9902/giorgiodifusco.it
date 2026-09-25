<?php ob_start(); ?>

<?php
$pendingBalance = $stats['pending_balance'] ?? 0;
$totalPaid = $stats['total_paid'] ?? 0;
$usedMinutes = $stats['lessons_completed_minutes'] ?? 0;
$remainingMinutes = $stats['packages_remaining_minutes'] ?? 0;

$usedH = floor($usedMinutes / 60);
$usedM = $usedMinutes % 60;
$usedStr = $usedH . 'h ' . ($usedM > 0 ? $usedM . 'm' : '');

$remH = floor($remainingMinutes / 60);
$remM = $remainingMinutes % 60;
$remStr = $remH . 'h ' . ($remM > 0 ? $remM . 'm' : '');

$nextLesson = $stats['next_lesson'] ?? null;
?>
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Area studente</h1>
        <p class="text-sm text-slate-400">Una panoramica veloce delle tue lezioni e della situazione pagamenti.</p>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Saldo Pendente -->
        <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-5">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Da saldare</h2>
            <p
                class="text-3xl font-bold tracking-tight <?= $pendingBalance > 0 ? 'text-red-400' : 'text-emerald-400' ?>">
                <?= number_format($pendingBalance, 2, ',', '.') ?> &euro;
            </p>
            <p class="text-[10px] text-slate-500 mt-2">Somma di pacchetti/lezioni in sospeso</p>
        </div>

        <!-- Totale Pagato -->
        <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-5">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Totale pagato</h2>
            <p class="text-3xl font-bold tracking-tight text-slate-100">
                <?= number_format($totalPaid, 2, ',', '.') ?> &euro;
            </p>
            <p class="text-[10px] text-slate-500 mt-2">Storico di tutti i tuoi versamenti</p>
        </div>

        <!-- Ore Svolte -->
        <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-5">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Ore svolte</h2>
            <p class="text-3xl font-bold tracking-tight text-indigo-400">
                <?= $usedMinutes > 0 ? $usedStr : '0h' ?>
            </p>
            <p class="text-[10px] text-slate-500 mt-2">Tempo totale di lezione erogato</p>
        </div>

        <!-- Ore Mancanti -->
        <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-5">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Ore residue</h2>
            <p
                class="text-3xl font-bold tracking-tight <?= $remainingMinutes > 0 ? 'text-emerald-400' : 'text-amber-400' ?>">
                <?= $remainingMinutes > 0 ? $remStr : '0h' ?>
            </p>
            <p class="text-[10px] text-slate-500 mt-2">Ore di lezione ancora a tua disposizione</p>
        </div>
    </div>

    <!-- Prossima Lezione -->
    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6">
        <h2 class="text-sm font-semibold text-slate-100 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Prossima Lezione
        </h2>
        <?php if ($nextLesson): ?>
            <?php
            $dateObj = new DateTime($nextLesson['date']);
            $dateStr = $dateObj->format('d/m/Y H:i');
            ?>
            <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-xl border border-slate-700">
                <div>
                    <p class="text-sm font-medium text-slate-200">
                        <?= htmlspecialchars($nextLesson['topic'] ?: 'Senza argomento') ?> (<?= $nextLesson['duration'] ?>
                        min)</p>
                    <p class="text-xs text-amber-400 mt-1 font-semibold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <?= $dateStr ?>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <p
                class="text-sm text-slate-400 p-4 bg-slate-800/30 rounded-xl border border-slate-800 border-dashed text-center">
                Al momento non ci sono lezioni in programma.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>