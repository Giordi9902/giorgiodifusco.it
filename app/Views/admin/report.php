<?php
// report.php - Pagina report docente
?>
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-indigo-400 mb-4">Report Docente</h1>
    <div class="bg-slate-900 rounded-lg p-6 shadow">
        <p class="text-slate-300 mb-4">Qui puoi visualizzare i report delle attività dei docenti.</p>
        <!-- Esempio tabella report -->
        <table class="min-w-full divide-y divide-slate-800">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400">Docente</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400">Lezioni svolte</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400">Studenti seguiti</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400">Ultima attività</th>
                </tr>
            </thead>
            <tbody>
                <!-- Qui andranno i dati dinamici -->
                <tr>
                    <td class="px-4 py-2 text-slate-200">Mario Rossi</td>
                    <td class="px-4 py-2 text-slate-200">12</td>
                    <td class="px-4 py-2 text-slate-200">8</td>
                    <td class="px-4 py-2 text-slate-200">2026-03-18</td>
                </tr>
                <tr>
                    <td class="px-4 py-2 text-slate-200">Lucia Bianchi</td>
                    <td class="px-4 py-2 text-slate-200">9</td>
                    <td class="px-4 py-2 text-slate-200">5</td>
                    <td class="px-4 py-2 text-slate-200">2026-03-19</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>