<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-50">Lezioni</h1>
        <p class="text-sm text-slate-400">Pianifica, modifica o annulla le lezioni per i tuoi studenti.</p>
    </div>

    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-slate-100">Calendario lezioni</h2>
                <p class="text-xs text-slate-400">Elenco delle lezioni singole e di pacchetto.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" onclick="loadAdminLessons()"
                    class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-700 transition">
                    Aggiorna elenco
                </button>
            </div>
        </div>

        <div class="space-y-3 mt-4" id="adminLessonsList">
            <!-- Popolato via JS -->
        </div>
    </div>
</div>

<script>
    let adminLessons = [];
    let adminLessonsVisibleCount = 10;
    const ADMIN_LESSONS_CHUNK = 10;

    async function loadAdminLessons() {
        try {
            const response = await fetch(`${BASE_URL}/api/lessons`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if (!result.success) return;

            adminLessons = (result.data || []).slice().sort((a, b) => new Date(b.date) - new Date(a.date));
            adminLessonsVisibleCount = ADMIN_LESSONS_CHUNK;
            renderAdminLessons();
        } catch (error) {
            console.error('Error loading lessons:', error);
        }
    }

    function renderAdminLessons() {
        const list = document.getElementById('adminLessonsList');
        if (!list) return;

        list.innerHTML = '';

        if (!adminLessons.length) {
            const empty = document.createElement('div');
            empty.className = 'text-center p-6 text-sm text-slate-500 border border-dashed border-slate-700 rounded-xl';
            empty.textContent = 'Nessuna lezione presente.';
            list.appendChild(empty);
            return;
        }

        const total = adminLessons.length;
        const visibleCount = Math.min(adminLessonsVisibleCount, total);

        for (let i = 0; i < visibleCount; i++) {
            const lesson = adminLessons[i];
            const item = document.createElement('div');
            item.className = 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900 p-4';

            const date = new Date(lesson.date);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = String(date.getFullYear());
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const dateStr = `${day}/${month}/${year} ${hours}:${minutes}`;
            const typeLabel = lesson.lesson_type === 'package' ? 'Pacchetto' : 'Singola';

            let statusBadgeClasses = 'bg-slate-500/10 text-slate-300';
            let statusText = lesson.status;
            if (lesson.status === 'scheduled') {
                statusBadgeClasses = 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20';
                statusText = 'Programmata';
            } else if (lesson.status === 'completed') {
                statusBadgeClasses = 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20';
                statusText = 'Completata';
            } else if (lesson.status === 'cancelled') {
                statusBadgeClasses = 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20';
                statusText = 'Cancellata';
            }

            item.innerHTML = `
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="text-sm font-semibold text-slate-100">${dateStr}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ${statusBadgeClasses}">
                            ${statusText}
                        </span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-indigo-500/10 text-indigo-300 ring-1 ring-indigo-500/20">
                            ${typeLabel} · ${lesson.duration} min
                        </span>
                    </div>
                    <h3 class="text-sm text-slate-300 mb-1">Studente: <span class="text-indigo-400 font-medium">${lesson.student_name || 'Sconosciuto'}</span></h3>
                    <p class="text-xs text-slate-400">Argomento: <span class="text-slate-300">${lesson.topic || 'Non specificato'}</span></p>
                </div>
                <div class="shrink-0 mt-3 sm:mt-0 flex gap-2">
                    <button class="text-xs font-medium text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 px-3 py-1.5 rounded-full transition">Modifica</button>
                </div>
            `;
            list.appendChild(item);
        }

        if (visibleCount < total) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex justify-center mt-3';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'px-4 py-2 rounded-full bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-100 border border-slate-700 transition';
            btn.textContent = 'Mostra altre lezioni';
            btn.addEventListener('click', () => {
                adminLessonsVisibleCount += ADMIN_LESSONS_CHUNK;
                renderAdminLessons();
            });

            wrapper.appendChild(btn);
            list.appendChild(wrapper);
        }
    }

    document.addEventListener('DOMContentLoaded', loadAdminLessons);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>