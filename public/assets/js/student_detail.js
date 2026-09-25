function switchTab(tabId) {
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.classList.remove("border-indigo-500", "text-indigo-400");
    btn.classList.add("border-transparent", "text-slate-400");
  });
  document.querySelectorAll(".tab-content").forEach((content) => {
    content.classList.add("hidden");
    content.classList.remove("block");
  });

  const activeBtn = document.getElementById("tab-" + tabId);
  activeBtn.classList.remove("border-transparent", "text-slate-400");
  activeBtn.classList.add("border-indigo-500", "text-indigo-400");

  const activeContent = document.getElementById("content-" + tabId);
  activeContent.classList.remove("hidden");
  activeContent.classList.add("block");

  // Lazy load data based on tab
  if (tabId === "lezioni") loadLessons();
  if (tabId === "registro") loadNotes();
  if (tabId === "pacchetti") loadPackages();
  if (tabId === "saldo") loadPayments();
  if (tabId === "materiale") loadMaterials();
}

// Stato per lo storico lezioni (tab Lezioni)
let allStudentLessons = [];
let pastLessonsForStudent = [];
let pastLessonsVisibleCount = 10;
const PAST_LESSONS_CHUNK = 10;

let quill;
document.addEventListener("DOMContentLoaded", () => {
  // Load default tab data immediately, so if anything below fails, the page still works
  loadLessons();

  const locationTypeSelect = document.getElementById("lessonLocationType");
  const meetingWrapper = document.getElementById("lessonMeetingLinkWrapper");
  if (locationTypeSelect && meetingWrapper) {
    locationTypeSelect.addEventListener("change", () => {
      if (locationTypeSelect.value === "online") {
        meetingWrapper.classList.remove("hidden");
      } else {
        meetingWrapper.classList.add("hidden");
      }
    });
  }

  try {
    // Initialize Quill with KaTeX integration
    if (typeof katex !== "undefined") {
      window.katex = katex;
    }
    if (typeof Quill !== "undefined") {
      quill = new Quill("#editor-container", {
        theme: "snow",
        modules: {
          // formula module disabled to avoid errors
          toolbar: [
            [{ header: [1, 2, 3, false] }],
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }],
            ["link", "image"],
            ["clean"],
          ],
        },
      });
    }
  } catch (e) {
    console.warn("WYSIWYG Editor non inizializzato:", e.message);
  }
});

// Toast helper
function showToast(message, type = "info") {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const toast = document.createElement("div");
  let baseClasses =
    "px-4 py-2 rounded-lg shadow-lg text-sm flex items-center gap-2 border transition-opacity bg-slate-900/95";
  let typeClasses = "border-slate-700 text-slate-100";

  if (type === "success") {
    typeClasses = "border-emerald-500/60 text-emerald-100 bg-emerald-900/80";
  } else if (type === "error") {
    typeClasses = "border-red-500/60 text-red-100 bg-red-900/80";
  } else if (type === "warning") {
    typeClasses = "border-amber-500/60 text-amber-100 bg-amber-900/80";
  }

  toast.className = `${baseClasses} ${typeClasses}`;
  toast.textContent = message;

  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add("opacity-0");
    setTimeout(() => {
      toast.remove();
    }, 300);
  }, 3000);
}

async function loadLessons() {
  try {
    const response = await fetch(`${BASE_URL}/api/lessons`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();

    const futureList = document.getElementById("futureLessonsList");
    const pastList = document.getElementById("pastLessonsList");
    if (!futureList || !pastList) return;

    futureList.innerHTML = "";
    pastList.innerHTML = "";

    if (!result.success || !result.data) {
      futureList.innerHTML = `<p class="text-sm text-red-500 text-center py-4">Errore dal server: ${result.message || "Nessun dato restituito"}</p>`;
      return;
    }

    const studentData = result.data.filter((l) => l.student_id == studentId);
    allStudentLessons = studentData;
    const now = new Date();

    const futureLessons = studentData.filter((l) => new Date(l.date) >= now);
    const pastLessons = studentData.filter((l) => new Date(l.date) < now);

    // ordina le lezioni passate dalla più recente alla più vecchia
    pastLessons.sort((a, b) => new Date(b.date) - new Date(a.date));
    pastLessonsForStudent = pastLessons;
    pastLessonsVisibleCount = PAST_LESSONS_CHUNK;

    // Lezioni future
    if (futureLessons.length === 0) {
      futureList.innerHTML =
        '<p class="text-sm text-slate-500 text-center py-4 border border-dashed border-slate-700 rounded-xl">Nessuna lezione futura programmata.</p>';
    } else {
      futureLessons.forEach((lesson) => {
        const dateObj = new Date(lesson.date);
        const day = String(dateObj.getDate()).padStart(2, "0");
        const month = String(dateObj.getMonth() + 1).padStart(2, "0");
        const year = String(dateObj.getFullYear());
        const hours = String(dateObj.getHours()).padStart(2, "0");
        const minutes = String(dateObj.getMinutes()).padStart(2, "0");
        const dateStr = `${day}/${month}/${year} ${hours}:${minutes}`;
        const typeLabel =
          lesson.lesson_type === "package" ? "Pacchetto" : "Singola";

        let statusBadgeClasses = "bg-slate-500/10 text-slate-300";
        let statusText = lesson.status;
        if (lesson.status === "scheduled") {
          statusBadgeClasses =
            "bg-amber-500/10 text-amber-300 ring-1 ring-amber-500/20";
          statusText = "Programmata";
        } else if (lesson.status === "completed") {
          statusBadgeClasses =
            "bg-emerald-500/10 text-emerald-300 ring-1 ring-emerald-500/20";
          statusText = "Completata";
        } else if (lesson.status === "cancelled") {
          statusBadgeClasses =
            "bg-red-500/10 text-red-300 ring-1 ring-red-500/20";
          statusText = "Cancellata";
        }

        const paidBadge =
          lesson.is_paid == 1
            ? '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 ml-2">Pagata</span>'
            : '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-red-500/10 text-red-400 ring-1 ring-red-500/20 ml-2">Da saldare</span>';
        const priceLabel = lesson.price
          ? ` - €${parseFloat(lesson.price).toFixed(2)}`
          : "";

        futureList.innerHTML += `
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-sm font-semibold text-slate-100">${dateStr}</span>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ${statusBadgeClasses}">
                  ${statusText}
                </span>
              </div>
              <p class="text-xs text-slate-400 mb-1">Argomento: <span class="text-slate-300">${lesson.topic || "Da definire"}</span> ${lesson.lesson_type === "single" ? paidBadge : ""} ${priceLabel}</p>
              <div class="flex items-center gap-3 text-[11px] text-slate-500">
                <span>Tipo: ${typeLabel}</span>
                <span>&bull;</span>
                <span>Durata: ${lesson.duration} min</span>
              </div>
            </div>
            <div class="shrink-0 flex gap-2">
              <button onclick="editLesson(${lesson.id})" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1.5 rounded-full transition">Modifica</button>
              <button onclick="deleteLesson(${lesson.id})" class="text-xs font-medium text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded-full transition">Elimina</button>
            </div>
          </div>
        `;
      });
    }

    // Lezioni passate (storico con caricamento progressivo)
    renderPastLessons();
  } catch (e) {
    const list = document.getElementById("futureLessonsList");
    if (list)
      list.innerHTML = `<p class="text-sm text-red-500 text-center py-4">Errore irreversibile: ${e.message}</p>`;
    console.error("loadLessons Error:", e);
  }
}

function renderPastLessons() {
  const pastList = document.getElementById("pastLessonsList");
  if (!pastList) return;

  pastList.innerHTML = "";

  if (!pastLessonsForStudent.length) {
    pastList.innerHTML =
      '<p class="text-sm text-slate-500 text-center py-4">Nessuno storico lezioni.</p>';
    return;
  }

  const total = pastLessonsForStudent.length;
  const visibleCount = Math.min(pastLessonsVisibleCount, total);

  for (let i = 0; i < visibleCount; i += 1) {
    const lesson = pastLessonsForStudent[i];
    const d = new Date(lesson.date);
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = String(d.getFullYear());
    const hours = String(d.getHours()).padStart(2, "0");
    const minutes = String(d.getMinutes()).padStart(2, "0");
    const date = `${day}/${month}/${year} ${hours}:${minutes}`;

    const paidBadge =
      lesson.is_paid == 1
        ? '<span class="text-[10px] bg-emerald-500/10 text-emerald-400 px-1.5 py-0.5 rounded ml-2">Pagata</span>'
        : '<span class="text-[10px] bg-red-500/10 text-red-400 px-1.5 py-0.5 rounded ml-2">Da saldare</span>';
    const priceLabel = lesson.price
      ? ` - €${parseFloat(lesson.price).toFixed(2)}`
      : "";

    let statusText = lesson.status;
    if (lesson.status === "scheduled") {
      statusText = "Programmata";
    } else if (lesson.status === "completed") {
      statusText = "Completata";
    } else if (lesson.status === "cancelled") {
      statusText = "Cancellata";
    }

    pastList.innerHTML += `
      <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg border border-slate-700">
        <div>
          <p class="text-sm font-medium text-slate-200">${lesson.topic || "Senza argomento"} (${lesson.duration} min) ${priceLabel} ${lesson.lesson_type === "single" ? paidBadge : ""}</p>
          <p class="text-xs text-slate-400">${date} - ${statusText}</p>
        </div>
        <div class="flex gap-2">
          <button onclick="editLesson(${lesson.id})" class="text-xs text-indigo-400 hover:underline">Modifica</button>
          <button onclick="deleteLesson(${lesson.id})" class="text-xs text-red-400 hover:underline">Elimina</button>
        </div>
      </div>
    `;
  }

  // Bottone "Vedi altre lezioni" se ci sono altri elementi oltre a quelli visibili
  if (visibleCount < total) {
    const wrapper = document.createElement("div");
    wrapper.className = "flex justify-center mt-3";

    const btn = document.createElement("button");
    btn.type = "button";
    btn.className =
      "px-4 py-2 rounded-full bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-100 border border-slate-700 transition";
    btn.textContent = "Vedi altre lezioni";
    btn.addEventListener("click", () => {
      pastLessonsVisibleCount += PAST_LESSONS_CHUNK;
      renderPastLessons();
    });

    wrapper.appendChild(btn);
    pastList.appendChild(wrapper);
  }
}

async function loadPackages() {
  try {
    const response = await fetch(`${BASE_URL}/api/packages`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    const list = document.getElementById("packagesList");
    list.innerHTML = "";

    if (!result.success || !result.data) return;
    const studentData = result.data.filter((p) => p.student_id == studentId);

    if (studentData.length === 0) {
      list.innerHTML =
        '<p class="text-sm text-slate-500 text-center py-4">Nessun pacchetto presente.</p>';
      return;
    }

    studentData.forEach((pkg) => {
      const d = new Date(pkg.created_at);
      const day = String(d.getDate()).padStart(2, "0");
      const month = String(d.getMonth() + 1).padStart(2, "0");
      const year = String(d.getFullYear());
      const date = `${day}/${month}/${year}`;
      const price =
        pkg.price !== null ? ` | € ${parseFloat(pkg.price).toFixed(2)}` : "";
      const paidBadge =
        pkg.is_paid == 1
          ? '<span class="text-[10px] bg-emerald-500/10 text-emerald-400 px-1.5 py-0.5 rounded ml-2">Pagato</span>'
          : '<span class="text-[10px] bg-red-500/10 text-red-400 px-1.5 py-0.5 rounded ml-2">Da saldare</span>';

      let lessonsHtml = "";
      let usedMinutes = 0;
      if (pkg.lessons && pkg.lessons.length > 0) {
        lessonsHtml =
          '<div class="mt-3 space-y-2 border-t border-slate-700 pt-3">';
        pkg.lessons.forEach((l) => {
          const lDate = new Date(l.date).toLocaleString("it-IT", {
            dateStyle: "short",
            timeStyle: "short",
          });

          if (l.status === "scheduled" || l.status === "completed") {
            const dur = parseInt(l.duration || 0, 10);
            if (!isNaN(dur)) usedMinutes += dur;
          }

          lessonsHtml += `
            <div class="flex justify-between items-center bg-slate-900/50 p-2 rounded">
              <div>
                <p class="text-xs text-slate-300 font-medium">${l.topic || "Senza argomento"} (${l.duration} min)</p>
                <p class="text-[10px] text-slate-500">${lDate} - ${l.status}</p>
              </div>
            </div>
          `;
        });
        lessonsHtml += "</div>";
      }

      const totalMinutes = parseInt(pkg.total_minutes || 0, 10);
      const remainingMinutes = Math.max(0, totalMinutes - usedMinutes);

      list.innerHTML += `
        <div class="p-4 bg-slate-800/50 rounded-lg border border-slate-700">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-200">${pkg.description || "Pacchetto ore"}</p>
              <p class="text-xs text-slate-400">Acquistato: ${date} - ${pkg.total_minutes} min ${price} ${paidBadge}</p>
              <p class="text-xs text-slate-400 mt-1">Minuti residui: <span class="font-semibold ${remainingMinutes > 0 ? "text-emerald-400" : "text-red-400"}">${remainingMinutes}</span></p>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold px-2 py-1 rounded bg-slate-700 text-slate-200">${pkg.status}</span>
              <button onclick="openModal('lessonModal', ${pkg.id}, ${remainingMinutes})" class="text-[10px] font-semibold bg-indigo-600/20 text-indigo-400 hover:bg-indigo-600 hover:text-white px-2 py-1 rounded transition border border-indigo-500/30">
                + Lezione
              </button>
            </div>
          </div>
          ${lessonsHtml}
        </div>
      `;
    });
  } catch (e) {
    console.error(e);
  }
}

async function loadPayments() {
  try {
    const response = await fetch(`${BASE_URL}/api/payments`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    const list = document.getElementById("paymentsList");
    list.innerHTML = "";

    if (!result.success || !result.data) return;
    const studentData = result.data.filter((p) => p.student_id == studentId);

    if (studentData.length === 0) {
      list.innerHTML =
        '<p class="text-sm text-slate-500 text-center py-4">Nessun pagamento registrato.</p>';
      return;
    }

    studentData.forEach((pay) => {
      const d = new Date(pay.date);
      const day = String(d.getDate()).padStart(2, "0");
      const month = String(d.getMonth() + 1).padStart(2, "0");
      const year = String(d.getFullYear());
      const date = `${day}/${month}/${year}`;
      list.innerHTML += `
                    <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg border border-slate-700">
                        <div>
                            <p class="text-sm font-medium text-emerald-400">€ ${parseFloat(pay.amount).toFixed(2)}</p>
                            <p class="text-xs text-slate-400">${date} - ${pay.method || "Non specificato"}${pay.package_id ? ` - Pacchetto: ${pay.package_description || "#" + pay.package_id}` : ""}</p>
                        </div>
                        ${pay.notes ? `<span class="text-xs text-slate-500 truncate max-w-xs" title="${pay.notes}">${pay.notes}</span>` : ""}
                    </div>
                `;
    });
  } catch (e) {
    console.error(e);
  }
}

async function loadNotes() {
  try {
    const response = await fetch(`${BASE_URL}/api/notes/${studentId}`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    const list = document.getElementById("notesList");
    if (!list) return;

    list.innerHTML = "";
    if (!result.success || !result.data || result.data.length === 0) {
      list.innerHTML =
        '<p class="text-sm text-slate-500 text-center py-4">Nessuna nota presente.</p>';
      return;
    }

    result.data.forEach((note) => {
      const d = new Date(note.created_at);
      const day = String(d.getDate()).padStart(2, "0");
      const month = String(d.getMonth() + 1).padStart(2, "0");
      const year = String(d.getFullYear());
      const date = `${day}/${month}/${year}`;
      list.innerHTML += `
                    <div class="p-4 bg-slate-800/50 rounded-lg border border-slate-700">
                        <div class="text-xs text-slate-400 mb-2">${date}</div>
                        <div class="text-sm text-slate-200 whitespace-pre-wrap">${note.content}</div>
                    </div>
                `;
    });
  } catch (e) {
    console.error("Error loading notes", e);
  }
}

async function saveNote(e) {
  e.preventDefault();
  const content = document.getElementById("newNoteContent").value;
  if (!content) return;

  try {
    const response = await fetch(`${BASE_URL}/api/notes`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        student_id: studentId,
        content: content,
        csrf_token: CSRF_TOKEN,
      }),
    });
    const result = await response.json();
    if (result.success) {
      document.getElementById("newNoteContent").value = "";
      loadNotes();
    } else {
      alert(result.message);
    }
  } catch (e) {
    alert("Errore nel salvataggio della nota");
  }
}

async function loadMaterials() {
  try {
    const response = await fetch(
      `${BASE_URL}/api/materials?student_id=${studentId}`,
      {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      },
    );
    const result = await response.json();
    const list = document.getElementById("materialsList");
    if (!list) return;

    list.innerHTML = "";
    if (!result.success || !result.data || result.data.length === 0) {
      list.innerHTML =
        '<p class="text-sm text-slate-500 text-center py-4">Nessun materiale condiviso.</p>';
      return;
    }

    const studentData = result.data.filter((m) => m.student_id == studentId);

    studentData.forEach((mat) => {
      const d = new Date(mat.uploaded_at);
      const day = String(d.getDate()).padStart(2, "0");
      const month = String(d.getMonth() + 1).padStart(2, "0");
      const year = String(d.getFullYear());
      const date = `${day}/${month}/${year}`;
      let icon = `<svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>`;
      let action = "";

      if (mat.type === "file") {
        action = `<a href="${BASE_URL}/public${mat.filepath}" target="_blank" class="text-xs text-indigo-400 hover:underline">Scarica</a>`;
      } else if (mat.type === "link") {
        icon = `<svg class="w-5 h-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>`;
        action = `<a href="${mat.url}" target="_blank" class="text-xs text-sky-400 hover:underline">Apri Link</a>`;
      } else {
        icon = `<svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>`;
        action = `<button onclick="showTextMaterial(${mat.id})" class="text-xs text-emerald-400 hover:underline">Leggi</button>
                              <div id="mat-content-${mat.id}" class="hidden">${mat.content}</div>`;
      }

      list.innerHTML += `
                    <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg border border-slate-700">
                        <div class="flex items-center gap-3">
                            ${icon}
                            <div>
                                <p class="text-sm font-medium text-slate-200">${mat.title || mat.filename || "Materiale"}</p>
                                <p class="text-xs text-slate-500">${date}</p>
                            </div>
                        </div>
                        ${action}
                    </div>
                `;
    });
  } catch (e) {
    console.error("Error loading materials", e);
  }
}

function showTextMaterial(id) {
  // A minimal modal or expand could go here. For now, simple alert or populating a div.
  const content = document.getElementById("mat-content-" + id).innerHTML;
  showToast(
    "Contenuto (preview, in futuro verrà mostrato in una modale dedicata).",
    "info",
  );
}

async function saveTextMaterial() {
  const content = quill.root.innerHTML;
  if (quill.getText().trim().length === 0) {
    showToast("Inserisci del testo.", "warning");
    return;
  }

  try {
    const response = await fetch(`${BASE_URL}/api/materials`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        student_id: studentId,
        type: "text",
        title:
          "Nota " +
          (() => {
            const d = new Date();
            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const year = String(d.getFullYear());
            return `${day}/${month}/${year}`;
          })(),
        content: content,
        csrf_token: CSRF_TOKEN,
      }),
    });
    const result = await response.json();
    if (result.success) {
      quill.setContents([]);
      loadMaterials();
    } else {
      showToast(
        result.message || "Errore nel salvataggio del materiale.",
        "error",
      );
    }
  } catch (e) {
    showToast("Errore nel salvataggio del materiale.", "error");
  }
}

async function saveFileMaterial() {
  const fileInput = document.getElementById("materialFile");
  if (!fileInput.files.length) return;

  const formData = new FormData();
  formData.append("student_id", studentId);
  formData.append("type", "file");
  formData.append("title", fileInput.files[0].name);
  formData.append("file", fileInput.files[0]);
  formData.append("csrf_token", CSRF_TOKEN);

  try {
    const response = await fetch(`${BASE_URL}/api/materials`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    });
    const result = await response.json();
    if (result.success) {
      fileInput.value = "";
      loadMaterials();
    } else {
      showToast(result.message || "Errore nel caricamento del file.", "error");
    }
  } catch (e) {
    showToast("Errore nel caricamento del file.", "error");
  }
}

async function saveLinkMaterial() {
  const linkInput = document.getElementById("materialLink");
  if (!linkInput.value) return;

  try {
    const response = await fetch(`${BASE_URL}/api/materials`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        student_id: studentId,
        type: "link",
        title: linkInput.value,
        url: linkInput.value,
        csrf_token: CSRF_TOKEN,
      }),
    });
    const result = await response.json();
    if (result.success) {
      linkInput.value = "";
      loadMaterials();
    } else {
      showToast(result.message || "Errore nel salvataggio del link.", "error");
    }
  } catch (e) {
    showToast("Errore nel salvataggio del link.", "error");
  }
}

function openModal(id, param = null, remainingMinutes = null) {
  let modal = document.getElementById(id);
  modal.classList.remove("hidden");
  modal.classList.add("flex");

  // Setup defaults
  if (id === "paymentModal") {
    document.getElementById("paymentDate").valueAsDate = new Date();
    preparePaymentModal();
  }
  if (id === "lessonModal") {
    document.getElementById("lessonPackageId").value = param || "";
    const remainingInput = document.getElementById("lessonPackageRemaining");
    if (remainingInput) {
      remainingInput.value = remainingMinutes != null ? remainingMinutes : "";
    }
    const typeSelect = document.getElementById("lessonType");
    if (typeSelect) {
      typeSelect.value = param ? "package" : "single";
    }

    const locationTypeSelect = document.getElementById("lessonLocationType");
    const meetingWrapper = document.getElementById("lessonMeetingLinkWrapper");
    const meetingInput = document.getElementById("lessonMeetingLink");
    if (locationTypeSelect && meetingWrapper && meetingInput) {
      // Reset to default valori quando si apre in modalità "nuova lezione"
      if (!currentEditLessonId) {
        locationTypeSelect.value = "in_person";
        meetingInput.value = "";
        meetingWrapper.classList.add("hidden");
      }
    }

    if (param && remainingMinutes !== null && remainingMinutes <= 0) {
      showToast(
        "Non è possibile aggiungere altre lezioni a questo pacchetto: minuti esauriti.",
        "warning",
      );
    }
  }
}

function closeModal(id) {
  let modal = document.getElementById(id);
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

let currentEditLessonId = null;

function editLesson(id) {
  // Fetch lesson details briefly to populate
  fetch(`${BASE_URL}/api/lessons`, {
    headers: {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((res) => res.json())
    .then((result) => {
      if (result.success && result.data) {
        const lesson = result.data.find((l) => l.id == id);
        if (lesson) {
          document.getElementById("lessonDateTime").value = lesson.date.replace(
            " ",
            "T",
          );
          document.getElementById("lessonDuration").value = lesson.duration;
          document.getElementById("lessonTopic").value = lesson.topic;
          if (document.getElementById("lessonPrice"))
            document.getElementById("lessonPrice").value = lesson.price || "";
          if (document.getElementById("lessonIsPaid"))
            document.getElementById("lessonIsPaid").checked =
              lesson.is_paid == 1;
          document.getElementById("lessonPackageId").value =
            lesson.package_id || "";
          const typeSelect = document.getElementById("lessonType");
          if (typeSelect) {
            typeSelect.value =
              lesson.lesson_type === "package" ? "package" : "single";
          }

          const locationTypeSelect =
            document.getElementById("lessonLocationType");
          const meetingWrapper = document.getElementById(
            "lessonMeetingLinkWrapper",
          );
          const meetingInput = document.getElementById("lessonMeetingLink");
          if (locationTypeSelect && meetingWrapper && meetingInput) {
            const locType =
              lesson.location_type === "online" ? "online" : "in_person";
            locationTypeSelect.value = locType;
            meetingInput.value = lesson.meeting_link || "";
            if (locType === "online") {
              meetingWrapper.classList.remove("hidden");
            } else {
              meetingWrapper.classList.add("hidden");
            }
          }

          currentEditLessonId = id;

          // Show save button as Update
          const modal = document.getElementById("lessonModal");
          modal.querySelector("h3").textContent = "Modifica Lezione";

          openModal("lessonModal");
        }
      }
    });
}

// Modale di conferma generica
let confirmResolve = null;

function openConfirm(message) {
  const msgEl = document.getElementById("confirmMessage");
  if (msgEl) msgEl.textContent = message;
  const modal = document.getElementById("confirmModal");
  if (modal) {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  }
  return new Promise((resolve) => {
    confirmResolve = resolve;
  });
}

function handleConfirmResult(result) {
  const modal = document.getElementById("confirmModal");
  if (modal) {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  }
  if (confirmResolve) {
    confirmResolve(result);
    confirmResolve = null;
  }
}

function confirmModalConfirm() {
  handleConfirmResult(true);
}

function confirmModalCancel() {
  handleConfirmResult(false);
}

async function deleteLesson(id) {
  const confirmed = await openConfirm(
    "Sei sicuro di voler eliminare questa lezione?",
  );
  if (!confirmed) return;

  try {
    const response = await fetch(`${BASE_URL}/api/lessons/${id}`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({ csrf_token: CSRF_TOKEN }),
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => null);
      const message =
        (errorData && (errorData.error || errorData.message)) ||
        "Errore durante l'eliminazione della lezione.";
      showToast(message, "error");
      return;
    }

    showToast("Lezione eliminata correttamente.", "success");

    // Ricarica lezioni e pacchetti (per aggiornare i minuti residui)
    await loadLessons();
    await loadPackages();
  } catch (error) {
    console.error("Errore nella richiesta di eliminazione lezione", error);
    showToast(
      "Si è verificato un errore di rete durante l'eliminazione della lezione.",
      "error",
    );
  }
}

async function submitLesson(e) {
  e.preventDefault();
  const date = document.getElementById("lessonDateTime").value;
  const duration = document.getElementById("lessonDuration").value;
  const topic = document.getElementById("lessonTopic").value;
  let packageId = document.getElementById("lessonPackageId").value;
  const remainingStr = document.getElementById("lessonPackageRemaining")
    ? document.getElementById("lessonPackageRemaining").value
    : "";
  const remainingMinutes = remainingStr ? parseInt(remainingStr, 10) : null;
  const price = document.getElementById("lessonPrice")
    ? document.getElementById("lessonPrice").value
    : null;
  const isPaid = document.getElementById("lessonIsPaid")
    ? document.getElementById("lessonIsPaid").checked
    : false;
  const typeSelect = document.getElementById("lessonType");
  const lessonType = typeSelect
    ? typeSelect.value
    : packageId
      ? "package"
      : "single";

  const locationTypeSelect = document.getElementById("lessonLocationType");
  const locationType = locationTypeSelect
    ? locationTypeSelect.value
    : "in_person";
  const meetingInput = document.getElementById("lessonMeetingLink");
  const meetingLink = meetingInput ? meetingInput.value.trim() : "";

  if (locationType === "online" && !meetingLink) {
    showToast(
      "Per una lezione online devi specificare un link di riunione.",
      "warning",
    );
    return;
  }

  if (lessonType === "package" && !packageId) {
    showToast(
      'Per associare una lezione a un pacchetto, apri il modulo dal tab "Pacchetti" usando il pulsante "+ Lezione" sul pacchetto desiderato.',
      "warning",
    );
    return;
  }

  if (lessonType === "package") {
    const durationMinutes = parseInt(duration || 0, 10);
    if (!isNaN(durationMinutes) && remainingMinutes !== null) {
      if (remainingMinutes <= 0) {
        showToast(
          "Il pacchetto selezionato non ha minuti residui disponibili per nuove lezioni.",
          "warning",
        );
        return;
      }
      if (durationMinutes > remainingMinutes) {
        showToast(
          "Durata della lezione superiore ai minuti residui del pacchetto (minuti disponibili: " +
            remainingMinutes +
            ").",
          "warning",
        );
        return;
      }
    }
  }

  if (lessonType === "single") {
    packageId = "";
    document.getElementById("lessonPackageId").value = "";
  }

  const method = currentEditLessonId ? "PUT" : "POST";
  const url = currentEditLessonId
    ? `${BASE_URL}/api/lessons/${currentEditLessonId}`
    : `${BASE_URL}/api/lessons`;

  try {
    const response = await fetch(url, {
      method: method,
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        student_id: studentId,
        date: date.replace("T", " "),
        duration: duration,
        topic: topic,
        lesson_type: lessonType,
        package_id: packageId,
        price: price,
        is_paid: isPaid,
        location_type: locationType,
        meeting_link: meetingLink,
        csrf_token: CSRF_TOKEN,
      }),
    });
    const result = await response.json();
    if (result.success) {
      closeModal("lessonModal");
      document.getElementById("lessonForm").reset();
      currentEditLessonId = null;
      document.getElementById("lessonModal").querySelector("h3").textContent =
        "Associa Nuova Lezione";
      loadLessons();
      loadPackages(); // Refresh packages in case payment status changed
    } else {
      showToast(
        result.message || "Errore nel salvataggio della lezione.",
        "error",
      );
    }
  } catch (e) {
    showToast("Errore del server durante il salvataggio.", "error");
  }
}

async function submitPackage(e) {
  e.preventDefault();
  const hours = document.getElementById("packageHours").value;
  const desc = document.getElementById("packageDesc").value;
  const status = document.getElementById("packageStatus").value;
  let created_at = document.getElementById("packageCreatedAt").value;
  const price = document.getElementById("packagePrice").value;
  const isPaid = document.getElementById("packageIsPaid")
    ? document.getElementById("packageIsPaid").checked
    : false;

  if (created_at) {
    created_at = created_at.replace("T", " ") + ":00";
  }

  try {
    const response = await fetch(`${BASE_URL}/api/packages`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        student_id: studentId,
        total_minutes: hours * 60,
        description: desc,
        status: status,
        created_at: created_at,
        price: price,
        is_paid: isPaid,
        csrf_token: CSRF_TOKEN,
      }),
    });
    const result = await response.json();
    if (result.success) {
      closeModal("packageModal");
      document.getElementById("packageForm").reset();
      loadPackages();
    } else {
      showToast(
        result.message || "Errore nel salvataggio del pacchetto.",
        "error",
      );
    }
  } catch (e) {
    showToast("Errore nel salvataggio del pacchetto.", "error");
  }
}

// Pacchetti non ancora saldati dello studente (per il modale pagamenti)
let unpaidPackagesForPayment = [];

function formatEuro(value) {
  return `€ ${Number(value || 0).toFixed(2)}`;
}

function getUnpaidSingleLessons() {
  return allStudentLessons
    .filter(
      (l) =>
        l.lesson_type === "single" &&
        l.is_paid != 1 &&
        l.status !== "cancelled" &&
        parseFloat(l.price) > 0,
    )
    .sort((a, b) => new Date(a.date) - new Date(b.date));
}

async function preparePaymentModal() {
  document.getElementById("paymentType").value = "single";
  document.getElementById("paymentAmount").value = "";

  const lessonSelect = document.getElementById("paymentLessonId");
  lessonSelect.innerHTML =
    '<option value="">Nessuna lezione specifica</option>';
  getUnpaidSingleLessons().forEach((l) => {
    const d = new Date(l.date).toLocaleString("it-IT", {
      dateStyle: "short",
      timeStyle: "short",
    });
    const opt = document.createElement("option");
    opt.value = l.id;
    opt.dataset.amount = parseFloat(l.price).toFixed(2);
    opt.textContent = `${d} - ${l.topic || "Senza argomento"} (${formatEuro(l.price)})`;
    lessonSelect.appendChild(opt);
  });

  const packageSelect = document.getElementById("paymentPackageId");
  packageSelect.innerHTML = '<option value="">Caricamento...</option>';
  updatePaymentTypeFields();

  try {
    const response = await fetch(`${BASE_URL}/api/packages`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    unpaidPackagesForPayment = (result.data || []).filter(
      (p) =>
        p.student_id == studentId && p.is_paid != 1 && p.status !== "cancelled",
    );
  } catch (err) {
    unpaidPackagesForPayment = [];
    console.error(err);
  }

  packageSelect.innerHTML = "";
  unpaidPackagesForPayment.forEach((p) => {
    const price = p.price !== null ? parseFloat(p.price) : 0;
    const residual = Math.max(0, price - parseFloat(p.paid_amount || 0));
    const d = new Date(p.created_at).toLocaleDateString("it-IT");
    const opt = document.createElement("option");
    opt.value = p.id;
    opt.dataset.amount = residual.toFixed(2);
    opt.textContent = `${p.description || "Pacchetto ore"} del ${d} - residuo ${formatEuro(residual)} su ${formatEuro(price)}`;
    packageSelect.appendChild(opt);
  });
  updatePaymentTypeFields();
}

function updatePaymentTypeFields() {
  const isPackage = document.getElementById("paymentType").value === "package";
  const packageSelect = document.getElementById("paymentPackageId");
  document
    .getElementById("paymentPackageWrapper")
    .classList.toggle("hidden", !isPackage);
  document
    .getElementById("paymentLessonWrapper")
    .classList.toggle("hidden", isPackage);
  document
    .getElementById("paymentPackageEmpty")
    .classList.toggle(
      "hidden",
      !isPackage || packageSelect.options.length > 0,
    );
  packageSelect.required = isPackage;
  prefillPaymentAmount();
}

function prefillPaymentAmount() {
  const isPackage = document.getElementById("paymentType").value === "package";
  const select = document.getElementById(
    isPackage ? "paymentPackageId" : "paymentLessonId",
  );
  const selected = select.options[select.selectedIndex];
  if (selected && selected.dataset.amount) {
    document.getElementById("paymentAmount").value = selected.dataset.amount;
  }
}

// Aggiorna i riquadri "Totale pagato" e "Da saldare" senza ricaricare la pagina
function adjustBalanceStats(paidAmount, settledAmount) {
  const parse = (el) =>
    parseFloat(el.textContent.replace(/[^\d,-]/g, "").replace(",", ".")) || 0;
  const fmt = (v) =>
    `€ ${v.toLocaleString("it-IT", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  const totalEl = document.getElementById("statTotalPaid");
  const pendingEl = document.getElementById("statPendingBalance");
  if (totalEl) totalEl.textContent = fmt(parse(totalEl) + paidAmount);
  if (pendingEl)
    pendingEl.textContent = fmt(Math.max(0, parse(pendingEl) - settledAmount));
}

async function submitPayment(e) {
  e.preventDefault();
  const amount = document.getElementById("paymentAmount").value;
  const date = document.getElementById("paymentDate").value;
  const method = document.getElementById("paymentMethod").value;
  const notes = document.getElementById("paymentNotes").value;
  const paymentType = document.getElementById("paymentType").value;
  const packageSelect = document.getElementById("paymentPackageId");
  const lessonSelect = document.getElementById("paymentLessonId");
  const packageId = paymentType === "package" ? packageSelect.value : "";
  const lessonId = paymentType === "single" ? lessonSelect.value : "";

  if (paymentType === "package" && !packageId) {
    showToast("Seleziona il pacchetto a cui collegare il pagamento.", "error");
    return;
  }

  // Quota del saldo pendente coperta da questo versamento
  const paid = parseFloat(amount) || 0;
  let settled = 0;
  if (packageId) {
    const opt = packageSelect.options[packageSelect.selectedIndex];
    settled = Math.min(paid, parseFloat(opt.dataset.amount) || 0);
  } else if (lessonId) {
    const opt = lessonSelect.options[lessonSelect.selectedIndex];
    settled = parseFloat(opt.dataset.amount) || 0;
  }

  try {
    const response = await fetch(`${BASE_URL}/api/payments`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        student_id: studentId,
        amount: amount,
        date: date,
        method: method,
        notes: notes,
        payment_type: paymentType,
        package_id: packageId,
        lesson_id: lessonId,
        csrf_token: CSRF_TOKEN,
      }),
    });
    const result = await response.json();
    if (result.success) {
      closeModal("paymentModal");
      document.getElementById("paymentForm").reset();
      adjustBalanceStats(paid, settled);
      loadPayments();
      loadPackages();
      loadLessons();
    } else {
      showToast(
        result.message || "Errore nel salvataggio del pagamento.",
        "error",
      );
    }
  } catch (e) {
    showToast("Errore nel salvataggio del pagamento.", "error");
  }
}
