<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Studenti</h1>
            <p class="text-sm text-slate-400">Elenco degli studenti che scelgono le tue lezioni.</p>
        </div>
        <button onclick="document.getElementById('addStudentModal').classList.remove('hidden')"
            class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 transition border border-indigo-400">
            Aggiungi studente
        </button>
    </div>

    <!-- Card studenti -->
    <div class="bg-slate-900/80 rounded-2xl shadow-xl shadow-indigo-900/10 border border-slate-800">
        <div class="border-b border-slate-800 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-100">Elenco Studenti</h2>
                <p class="text-xs text-slate-400">Tutti gli studenti registrati nella piattaforma.</p>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-3" id="studentsList">
                <!-- Popolato via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modale: aggiungi studente -->
<div id="addStudentModal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center z-50">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-md shadow-2xl shadow-black/50">
        <h3 class="text-lg font-semibold mb-4 text-slate-50">Nuovo studente</h3>
        <form id="addStudentForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300">Nome e cognome</label>
                <input type="text" name="name" required
                    class="mt-1 block w-full border border-slate-700 bg-slate-950/50 rounded-md shadow-sm p-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                    placeholder="Es. Mario Rossi">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300">Email</label>
                <input type="email" name="email" required
                    class="mt-1 block w-full border border-slate-700 bg-slate-950/50 rounded-md shadow-sm p-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                    placeholder="nome@esempio.it">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-300">Password iniziale</label>
                <input type="password" name="password" required
                    class="mt-1 block w-full border border-slate-700 bg-slate-950/50 rounded-md shadow-sm p-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                    placeholder="Imposta una password temporanea">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('addStudentModal').classList.add('hidden')"
                    class="px-4 py-2 border border-slate-700 rounded-full text-xs font-medium text-slate-300 hover:bg-slate-800 transition">Annulla</button>
                <button type="submit"
                    class="px-4 py-2 rounded-full border border-indigo-500 bg-indigo-600 text-xs font-semibold text-white hover:bg-indigo-500 shadow-lg shadow-indigo-900/20 transition">Salva
                    studente</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        loadStudents();

        document.getElementById('addStudentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data.csrf_token = CSRF_TOKEN;

            try {
                const response = await fetch(`${BASE_URL}/api/students`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                // Gestito da admin_students.js
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });

    async function loadStudents() {
        try {
            const response = await fetch(`${BASE_URL}/api/students`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            if (result.success) {
                const list = document.getElementById('studentsList');
                list.innerHTML = '';

                if (!result.data || result.data.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'text-center p-6 text-sm text-slate-500 border border-dashed border-slate-700 rounded-xl';
                    empty.textContent = 'Nessuno studente presente.';
                    list.appendChild(empty);
                    return;
                }

                result.data.forEach(student => {
                    const item = document.createElement('div');
                    item.className = 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900 p-4';

                    const d = new Date(student.created_at);
                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const year = String(d.getFullYear());
                    const dateStr = `${day}/${month}/${year}`;

                    item.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center text-sm font-semibold border border-slate-700">
                                ${student.name.substring(0, 1).toUpperCase()}
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-100">${student.name}</h3>
                                <p class="text-xs text-slate-400">${student.email}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">Iscritto il: ${dateStr}</p>
                            </div>
                        </div>
                        <div class="shrink-0 mt-2 sm:mt-0 flex gap-2">
                            <a href="${BASE_URL}/admin/students/${student.id}" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1.5 rounded-full transition">Gestisci</a>
                            <button onclick="deleteStudent(${student.id})" class="text-xs font-medium text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded-full transition">Elimina</button>
                        </div>
                    `;
                    list.appendChild(item);
                });
            }
        } catch (error) {
            console.error('Error loading students:', error);
        }
    }

    async function deleteStudent(id) {
        // Placeholder: logic moved to public/assets/js/admin_students.js
    }
</script>

<script src="<?= asset('assets/js/admin_students.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>