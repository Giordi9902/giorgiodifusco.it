<?php ob_start(); ?>
<?php
$pending = $stats['pending_balance'] ?? 0;
$paid = $stats['total_paid'] ?? 0;
$unpaidPackages = $stats['unpaid_packages'] ?? null;
$unpaidLessons = $stats['unpaid_single_lessons'] ?? null;
$notes = $notes ?? [];
?>
<div class="space-y-10 max-w-3xl mx-auto">
    <h1 class="text-3xl font-extrabold text-indigo-400 mb-6 text-center">Report Studente</h1>

    <!-- Sezione Pagamenti -->
    <div class="bg-slate-900 rounded-2xl p-8 shadow-xl border border-slate-800">
        <h2 class="text-xl font-bold text-slate-200 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Situazione Pagamenti
        </h2>
        <table class="min-w-full divide-y divide-slate-800 text-base">
            <tbody>
                <tr>
                    <td class="px-4 py-2 text-slate-300">Pacchetti non pagati</td>
                    <td class="px-4 py-2 text-slate-200 text-right">
                        <?= $unpaidPackages !== null ? number_format($unpaidPackages, 2, ',', '.') : '-' ?> &euro;
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-2 text-slate-300">Lezioni singole non pagate</td>
                    <td class="px-4 py-2 text-slate-200 text-right">
                        <?= $unpaidLessons !== null ? number_format($unpaidLessons, 2, ',', '.') : '-' ?> &euro;
                    </td>
                </tr>
                <tr class="border-t border-slate-800">
                    <td class="px-4 py-2 text-slate-200 font-semibold">Totale da saldare</td>
                    <td class="px-4 py-2 text-red-400 font-bold text-right">
                        <?= number_format($pending, 2, ',', '.') ?> &euro;
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-2 text-slate-300">Totale pagato</td>
                    <td class="px-4 py-2 text-emerald-400 font-bold text-right">
                        <?= number_format($paid, 2, ',', '.') ?> &euro;
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Sezione Resoconti Docente -->
    <div class="bg-slate-900 rounded-2xl p-8 shadow-xl border border-slate-800">
        <h2 class="text-xl font-bold text-slate-200 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9" />
            </svg>
            Resoconti del Docente
        </h2>
        <?php if (empty($notes)): ?>
            <div class="text-slate-400 italic">Nessun resoconto disponibile.</div>
        <?php else: ?>
            <ul class="space-y-6">
                <?php foreach ($notes as $note): ?>
                    <li class="bg-slate-800 rounded-lg p-5 shadow border border-slate-700">
                        <div class="text-slate-300 whitespace-pre-line mb-2">
                            <?= nl2br(htmlspecialchars($note['content'])) ?>
                        </div>
                        <div class="text-xs text-slate-500 text-right">
                            <?= date('d/m/Y', strtotime($note['created_at'] ?? $note['updated_at'] ?? '')) ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>