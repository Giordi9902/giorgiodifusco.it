<?php ob_start(); $layoutArea = 'cms'; ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Aree &amp; Argomenti</h1>
            <p class="text-sm text-slate-400">Organizza gli articoli in Aree tematiche e Argomenti specifici.</p>
        </div>
        <button type="button" id="addAreaBtn"
            class="inline-flex items-center justify-center rounded-full bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-purple-900/20 hover:bg-purple-500 transition border border-purple-500">
            + Nuova area
        </button>
    </div>

    <!-- Empty state -->
    <div id="areasEmpty" class="hidden py-16 text-center">
        <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-800 ring-1 ring-slate-700 mb-4">
            <svg class="w-7 h-7 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <p class="text-slate-400 text-sm">Nessuna area ancora creata.</p>
        <button type="button" onclick="document.getElementById('addAreaBtn').click()"
            class="mt-3 text-sm text-purple-400 hover:text-purple-300 underline underline-offset-2">Crea la prima area →</button>
    </div>

    <!-- Areas list -->
    <div id="areasList" class="space-y-4"></div>
</div>

<!-- Modal: add/edit area -->
<div id="areaModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
        <h2 id="areaModalTitle" class="text-lg font-semibold text-slate-50"></h2>
        <input type="hidden" id="areaModalId">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Nome area *</label>
            <input type="text" id="areaModalName"
                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg p-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition"
                placeholder="Es. Matematica, Informatica, Fisica…">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Descrizione <span class="text-slate-500 font-normal">(opzionale)</span></label>
            <textarea id="areaModalDescription" rows="2"
                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg p-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition resize-none"
                placeholder="Breve descrizione dell'area tematica"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Ordine <span class="text-slate-500 font-normal">(numerico, crescente)</span></label>
            <input type="number" id="areaModalOrder" value="0" min="0"
                class="block w-28 border border-slate-700 bg-slate-950/50 rounded-lg p-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition">
        </div>
        <div class="flex justify-end gap-3 pt-2 border-t border-slate-800">
            <button type="button" id="areaModalCancel"
                class="px-4 py-2 border border-slate-700 rounded-full text-sm font-medium text-slate-300 hover:bg-slate-800 transition">
                Annulla
            </button>
            <button type="button" id="areaModalSave"
                class="px-5 py-2 rounded-full border border-purple-500 bg-purple-600 text-sm font-semibold text-white hover:bg-purple-500 shadow-lg shadow-purple-900/20 transition">
                Salva
            </button>
        </div>
    </div>
</div>

<!-- Modal: add/edit subject -->
<div id="subjectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
        <h2 id="subjectModalTitle" class="text-lg font-semibold text-slate-50"></h2>
        <input type="hidden" id="subjectModalId">
        <input type="hidden" id="subjectModalCourseId">
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Nome argomento *</label>
            <input type="text" id="subjectModalName"
                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg p-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition"
                placeholder="Es. Algebra, SQL, Termodinamica…">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Descrizione <span class="text-slate-500 font-normal">(opzionale)</span></label>
            <textarea id="subjectModalDescription" rows="2"
                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg p-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition resize-none"
                placeholder="Breve descrizione dell'argomento"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Ordine</label>
            <input type="number" id="subjectModalOrder" value="0" min="0"
                class="block w-28 border border-slate-700 bg-slate-950/50 rounded-lg p-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition">
        </div>
        <div class="flex justify-end gap-3 pt-2 border-t border-slate-800">
            <button type="button" id="subjectModalCancel"
                class="px-4 py-2 border border-slate-700 rounded-full text-sm font-medium text-slate-300 hover:bg-slate-800 transition">
                Annulla
            </button>
            <button type="button" id="subjectModalSave"
                class="px-5 py-2 rounded-full border border-purple-500 bg-purple-600 text-sm font-semibold text-white hover:bg-purple-500 shadow-lg shadow-purple-900/20 transition">
                Salva
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    let areas = [];

    // ── Helpers ──────────────────────────────────────────────────────────────

    function api(method, url, body) {
        const opts = {
            method,
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        };
        if (body) opts.body = JSON.stringify({ ...body, csrf_token: CSRF_TOKEN });
        return fetch(BASE_URL + url, opts).then(r => r.json());
    }

    function alert(msg, variant) {
        if (window.showAppAlert) return window.showAppAlert(msg, { variant: variant || 'success' });
        return Promise.resolve(window.alert(msg));
    }

    function confirm(msg, title) {
        if (window.showAppConfirm) return window.showAppConfirm(msg, { title, primaryText: 'Elimina', secondaryText: 'Annulla', variant: 'danger' });
        return Promise.resolve(window.confirm(msg));
    }

    // ── Render ───────────────────────────────────────────────────────────────

    function renderAreas() {
        const list  = document.getElementById('areasList');
        const empty = document.getElementById('areasEmpty');
        list.innerHTML = '';

        if (!areas.length) {
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');

        areas.forEach(area => {
            const card = document.createElement('div');
            card.className = 'bg-slate-900/80 rounded-2xl border border-slate-800 shadow-xl overflow-hidden';
            card.dataset.areaId = area.id;

            const postCount    = parseInt(area.post_count    || 0);
            const subjectCount = parseInt(area.subject_count || 0);

            card.innerHTML = `
                <div class="flex items-start justify-between px-5 py-4 border-b border-slate-800 gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-semibold text-slate-50">${escHtml(area.name)}</h2>
                            <span class="inline-flex items-center rounded-full bg-purple-500/10 px-2 py-0.5 text-[11px] font-medium text-purple-400 ring-1 ring-purple-500/20">
                                ${postCount} articol${postCount === 1 ? 'o' : 'i'}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-slate-700/50 px-2 py-0.5 text-[11px] font-medium text-slate-400 ring-1 ring-slate-600/30">
                                ${subjectCount} argomento${subjectCount !== 1 ? 'i' : ''}
                            </span>
                        </div>
                        ${area.description ? `<p class="text-xs text-slate-500 mt-1">${escHtml(area.description)}</p>` : ''}
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="${BASE_URL}/blog?course=${encodeURIComponent(area.slug)}" target="_blank"
                            class="text-xs text-slate-500 hover:text-slate-300 transition" title="Vedi sul blog">↗</a>
                        <button class="text-xs font-medium text-purple-400 hover:text-purple-300 transition px-2 py-1 rounded hover:bg-purple-500/10"
                            data-action="add-subject" data-course-id="${area.id}" data-course-name="${escHtml(area.name)}">
                            + Argomento
                        </button>
                        <button class="text-xs font-medium text-slate-400 hover:text-slate-200 transition px-2 py-1 rounded hover:bg-slate-800"
                            data-action="edit-area" data-id="${area.id}" data-name="${escHtml(area.name)}"
                            data-description="${escHtml(area.description || '')}" data-order="${area.sort_order || 0}">
                            Modifica
                        </button>
                        <button class="text-xs font-medium text-red-400 hover:text-red-300 transition px-2 py-1 rounded hover:bg-red-500/10"
                            data-action="delete-area" data-id="${area.id}" data-name="${escHtml(area.name)}">
                            Elimina
                        </button>
                    </div>
                </div>
                <div class="subjects-container px-5 py-3" data-course-id="${area.id}">
                    <p class="text-xs text-slate-600 italic">Caricamento argomenti…</p>
                </div>`;

            list.appendChild(card);
            loadSubjects(area.id);
        });

        // Delegate clicks
        list.addEventListener('click', handleClick);
    }

    function renderSubjects(courseId, subjects) {
        const container = document.querySelector(`.subjects-container[data-course-id="${courseId}"]`);
        if (!container) return;

        if (!subjects.length) {
            container.innerHTML = '<p class="text-xs text-slate-600 italic py-1">Nessun argomento. Clicca "+ Argomento" per aggiungerne uno.</p>';
            return;
        }

        const rows = subjects.map(s => {
            const pc = parseInt(s.post_count || 0);
            return `<div class="flex items-center justify-between py-2 border-b border-slate-800/50 last:border-0 gap-2" data-subject-id="${s.id}">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-600 shrink-0"></span>
                    <span class="text-sm text-slate-200">${escHtml(s.name)}</span>
                    ${s.description ? `<span class="text-xs text-slate-500 truncate hidden sm:inline">${escHtml(s.description)}</span>` : ''}
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[11px] text-slate-500">${pc} art.</span>
                    <a href="${BASE_URL}/blog?course=&subject=${encodeURIComponent(s.slug)}" target="_blank"
                        class="text-[11px] text-slate-600 hover:text-slate-400 transition" title="Vedi sul blog">↗</a>
                    <button class="text-[11px] text-slate-400 hover:text-slate-200 transition px-1.5 py-0.5 rounded hover:bg-slate-800"
                        data-action="edit-subject" data-id="${s.id}" data-course-id="${courseId}"
                        data-name="${escHtml(s.name)}" data-description="${escHtml(s.description || '')}" data-order="${s.sort_order || 0}">
                        Modifica
                    </button>
                    <button class="text-[11px] text-red-400 hover:text-red-300 transition px-1.5 py-0.5 rounded hover:bg-red-500/10"
                        data-action="delete-subject" data-id="${s.id}" data-name="${escHtml(s.name)}" data-course-id="${courseId}">
                        Elimina
                    </button>
                </div>
            </div>`;
        }).join('');

        container.innerHTML = rows;
    }

    // ── Data loading ─────────────────────────────────────────────────────────

    async function loadAreas() {
        try {
            const res = await api('GET', '/api/blog/courses?stats');
            if (res.success) {
                areas = res.data || [];
                renderAreas();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function loadSubjects(courseId) {
        try {
            const res = await api('GET', `/api/blog/subjects?course_id=${courseId}&stats`);
            if (res.success) renderSubjects(courseId, res.data || []);
        } catch (e) { /* silent */ }
    }

    // ── Click handler ─────────────────────────────────────────────────────────

    function handleClick(e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const action = btn.dataset.action;

        if (action === 'edit-area') {
            openAreaModal({ id: btn.dataset.id, name: btn.dataset.name, description: btn.dataset.description, sort_order: btn.dataset.order });
        } else if (action === 'delete-area') {
            deleteArea(btn.dataset.id, btn.dataset.name);
        } else if (action === 'add-subject') {
            openSubjectModal({ courseId: btn.dataset.courseId, courseName: btn.dataset.courseName });
        } else if (action === 'edit-subject') {
            openSubjectModal({ id: btn.dataset.id, courseId: btn.dataset.courseId, name: btn.dataset.name, description: btn.dataset.description, sort_order: btn.dataset.order });
        } else if (action === 'delete-subject') {
            deleteSubject(btn.dataset.id, btn.dataset.name, btn.dataset.courseId);
        }
    }

    // ── Area modal ─────────────────────────────────────────────────────────

    function openAreaModal(data) {
        const isEdit = !!data.id;
        document.getElementById('areaModalTitle').textContent = isEdit ? 'Modifica area' : 'Nuova area';
        document.getElementById('areaModalId').value          = data.id || '';
        document.getElementById('areaModalName').value        = data.name || '';
        document.getElementById('areaModalDescription').value = data.description || '';
        document.getElementById('areaModalOrder').value       = data.sort_order || 0;
        document.getElementById('areaModal').classList.replace('hidden', 'flex');
        document.getElementById('areaModalName').focus();
    }

    function closeAreaModal() {
        document.getElementById('areaModal').classList.replace('flex', 'hidden');
    }

    async function saveArea() {
        const id          = document.getElementById('areaModalId').value;
        const name        = document.getElementById('areaModalName').value.trim();
        const description = document.getElementById('areaModalDescription').value.trim() || null;
        const sort_order  = parseInt(document.getElementById('areaModalOrder').value) || 0;

        if (!name) { await alert('Il nome dell\'area è obbligatorio', 'error'); return; }

        try {
            const res = id
                ? await api('PUT',  `/api/blog/courses/${id}`, { name, description, sort_order })
                : await api('POST', '/api/blog/courses',       { name, description, sort_order });

            if (res.success) {
                closeAreaModal();
                await loadAreas();
            } else {
                await alert(res.message || 'Errore', 'error');
            }
        } catch (e) { await alert('Errore di rete', 'error'); }
    }

    async function deleteArea(id, name) {
        if (!await confirm(`Eliminare l'area "${name}"? Gli articoli associati non saranno cancellati, ma perderanno il collegamento all'area.`, 'Elimina area')) return;
        try {
            const res = await api('DELETE', `/api/blog/courses/${id}`, {});
            if (res.success) { await loadAreas(); }
            else await alert(res.message || 'Errore', 'error');
        } catch (e) { await alert('Errore di rete', 'error'); }
    }

    // ── Subject modal ─────────────────────────────────────────────────────────

    function openSubjectModal(data) {
        const isEdit = !!data.id;
        document.getElementById('subjectModalTitle').textContent      = isEdit ? `Modifica argomento` : `Nuovo argomento`;
        document.getElementById('subjectModalId').value               = data.id || '';
        document.getElementById('subjectModalCourseId').value         = data.courseId || '';
        document.getElementById('subjectModalName').value             = data.name || '';
        document.getElementById('subjectModalDescription').value      = data.description || '';
        document.getElementById('subjectModalOrder').value            = data.sort_order || 0;
        document.getElementById('subjectModal').classList.replace('hidden', 'flex');
        document.getElementById('subjectModalName').focus();
    }

    function closeSubjectModal() {
        document.getElementById('subjectModal').classList.replace('flex', 'hidden');
    }

    async function saveSubject() {
        const id          = document.getElementById('subjectModalId').value;
        const courseId    = document.getElementById('subjectModalCourseId').value;
        const name        = document.getElementById('subjectModalName').value.trim();
        const description = document.getElementById('subjectModalDescription').value.trim() || null;
        const sort_order  = parseInt(document.getElementById('subjectModalOrder').value) || 0;

        if (!name) { await alert('Il nome dell\'argomento è obbligatorio', 'error'); return; }

        try {
            const res = id
                ? await api('PUT',  `/api/blog/subjects/${id}`, { name, description, sort_order, course_id: courseId })
                : await api('POST', '/api/blog/subjects',       { name, description, sort_order, course_id: courseId });

            if (res.success) {
                closeSubjectModal();
                await loadSubjects(courseId);
            } else {
                await alert(res.message || 'Errore', 'error');
            }
        } catch (e) { await alert('Errore di rete', 'error'); }
    }

    async function deleteSubject(id, name, courseId) {
        if (!await confirm(`Eliminare l'argomento "${name}"? Gli articoli associati non saranno cancellati.`, 'Elimina argomento')) return;
        try {
            const res = await api('DELETE', `/api/blog/subjects/${id}`, {});
            if (res.success) { await loadSubjects(courseId); }
            else await alert(res.message || 'Errore', 'error');
        } catch (e) { await alert('Errore di rete', 'error'); }
    }

    // ── Escape HTML ──────────────────────────────────────────────────────────

    function escHtml(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Wire up ──────────────────────────────────────────────────────────────

    document.getElementById('addAreaBtn').addEventListener('click', () => openAreaModal({}));
    document.getElementById('areaModalCancel').addEventListener('click', closeAreaModal);
    document.getElementById('areaModalSave').addEventListener('click', saveArea);
    document.getElementById('areaModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeAreaModal(); });
    document.getElementById('areaModalName').addEventListener('keydown', e => { if (e.key === 'Enter') saveArea(); });

    document.getElementById('subjectModalCancel').addEventListener('click', closeSubjectModal);
    document.getElementById('subjectModalSave').addEventListener('click', saveSubject);
    document.getElementById('subjectModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeSubjectModal(); });
    document.getElementById('subjectModalName').addEventListener('keydown', e => { if (e.key === 'Enter') saveSubject(); });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeAreaModal(); closeSubjectModal(); }
    });

    // Boot
    loadAreas();
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
