async function loadStudentNotes() {
  const list = document.getElementById("studentNotesList");
  if (!list) return;

  list.innerHTML =
    '<p class="text-sm text-slate-400">Caricamento delle note in corso...</p>';

  const studentId = CURRENT_USER_ID;
  if (!studentId) {
    list.innerHTML =
      '<p class="text-sm text-red-400">Impossibile determinare l\'utente corrente.</p>';
    return;
  }

  try {
    const response = await fetch(
      `${BASE_URL}/api/notes/${encodeURIComponent(studentId)}`,
      {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      },
    );

    const result = await response.json();

    if (!result.success) {
      list.innerHTML = `<p class=\"text-sm text-red-400\">${
        result.message || "Errore nel caricamento delle note."
      }</p>`;
      return;
    }

    const notes = result.data || [];
    list.innerHTML = "";

    if (!notes.length) {
      list.innerHTML =
        '<p class="text-sm text-slate-500">Non ci sono ancora note inserite dal docente.</p>';
      return;
    }

    notes.forEach((note) => {
      const createdAt = note.created_at ? new Date(note.created_at) : null;
      let dateStr = "";
      if (createdAt) {
        const d = String(createdAt.getDate()).padStart(2, "0");
        const m = String(createdAt.getMonth() + 1).padStart(2, "0");
        const y = String(createdAt.getFullYear());
        const hh = String(createdAt.getHours()).padStart(2, "0");
        const mm = String(createdAt.getMinutes()).padStart(2, "0");
        dateStr = `${d}/${m}/${y} ore ${hh}:${mm}`;
      }

      const item = document.createElement("div");
      item.className =
        "rounded-xl border border-slate-800 bg-slate-900/70 p-4 space-y-1";

      if (dateStr) {
        const dateEl = document.createElement("p");
        dateEl.className = "text-xs text-slate-500";
        dateEl.textContent = dateStr;
        item.appendChild(dateEl);
      }

      const contentEl = document.createElement("p");
      contentEl.className = "text-sm text-slate-200 whitespace-pre-line";
      contentEl.textContent = note.content || "";
      item.appendChild(contentEl);

      list.appendChild(item);
    });
  } catch (error) {
    console.error("Errore nel caricamento delle note studente", error);
    list.innerHTML =
      '<p class="text-sm text-red-400">Errore del server durante il caricamento delle note.</p>';
  }
}

document.addEventListener("DOMContentLoaded", () => {
  loadStudentNotes();
});
