let loadedLessons = [];
let currentPage = 1;
const PAGE_SIZE = 10;

async function loadStudentLessons() {
  try {
    const response = await fetch(`${BASE_URL}/api/lessons`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    if (!result.success) return;

    const list = document.getElementById("studentLessonsList");
    if (!list) return;

    loadedLessons = result.data || [];
    currentPage = 1;
    renderLessonsPage();
  } catch (error) {
    console.error("Error loading lessons:", error);
  }
}

function renderLessonsPage() {
  const list = document.getElementById("studentLessonsList");
  const paginationContainer = document.getElementById(
    "studentLessonsPagination",
  );
  if (!list) return;

  list.innerHTML = "";

  if (!loadedLessons.length) {
    const empty = document.createElement("div");
    empty.className =
      "text-center p-6 text-sm text-slate-500 border border-dashed border-slate-700 rounded-xl";
    empty.textContent = "Non ci sono ancora lezioni registrate.";
    list.appendChild(empty);
    if (paginationContainer) paginationContainer.classList.add("hidden");
    return;
  }

  const totalPages = Math.max(1, Math.ceil(loadedLessons.length / PAGE_SIZE));
  if (currentPage > totalPages) currentPage = totalPages;

  const start = (currentPage - 1) * PAGE_SIZE;
  const end = start + PAGE_SIZE;
  const pageItems = loadedLessons.slice(start, end);

  pageItems.forEach((lesson) => {
    const item = document.createElement("div");
    item.className =
      "flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900 p-4";

    const date = new Date(lesson.date);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = String(date.getFullYear());
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const dateStr = `${day}/${month}/${year} ${hours}:${minutes}`;
    const typeLabel =
      lesson.lesson_type === "package" ? "Pacchetto" : "Singola";
    const modeLabel =
      lesson.location_type === "online" ? "Online" : "In presenza";

    let statusBadgeClasses = "bg-slate-500/10 text-slate-300";
    let statusText = lesson.status;
    if (lesson.status === "scheduled") {
      statusBadgeClasses =
        "bg-amber-500/10 text-amber-300 ring-1 ring-amber-500/20";
      statusText = "Programmata";
    } else if (lesson.status === "completed") {
      statusBadgeClasses =
        "bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20";
      statusText = "Completata";
    } else if (lesson.status === "cancelled") {
      statusBadgeClasses = "bg-red-500/10 text-red-300 ring-1 ring-red-500/20";
      statusText = "Cancellata";
    }

    const joinButton =
      lesson.status === "scheduled" &&
      lesson.location_type === "online" &&
      lesson.meeting_link
        ? `<a href="${lesson.meeting_link}" target="_blank"
                class="inline-flex items-center justify-center text-xs font-medium text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 px-3 py-1.5 rounded-full transition">
                Entra in riunione
           </a>`
        : "";

    item.innerHTML = `
<div class="flex-1">
    <div class="flex items-center gap-2 mb-1">
        <span class="text-sm font-semibold text-slate-100">${dateStr}</span>
        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ${statusBadgeClasses}">
            ${statusText}
        </span>
    </div>
    <p class="text-xs text-slate-400 mb-1">Argomento: <span class="text-slate-300">${lesson.topic || "Da definire"}</span></p>
    <div class="flex items-center gap-3 text-[11px] text-slate-500">
        <span>Tipo: ${typeLabel}</span>
        <span>&bull;</span>
        <span>Modalità: ${modeLabel}</span>
        <span>&bull;</span>
        <span>Durata: ${lesson.duration} min</span>
    </div>
</div>
<div class="shrink-0 flex flex-col sm:flex-row gap-2 items-end sm:items-center">
    ${joinButton}
    <button data-lesson-id="${lesson.id}"
        class="js-open-lesson text-xs font-medium text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1.5 rounded-full transition">
        Vedi dettagli
    </button>
</div>`;

    list.appendChild(item);
  });

  if (paginationContainer) {
    paginationContainer.classList.remove("hidden");
    const pageInfo = paginationContainer.querySelector(
      '[data-role="page-info"]',
    );
    const prevBtn = paginationContainer.querySelector(
      '[data-role="prev-page"]',
    );
    const nextBtn = paginationContainer.querySelector(
      '[data-role="next-page"]',
    );

    if (pageInfo) {
      pageInfo.textContent = `Pagina ${currentPage} di ${totalPages}`;
    }
    if (prevBtn) {
      prevBtn.disabled = currentPage <= 1;
      prevBtn.classList.toggle("opacity-50", currentPage <= 1);
      prevBtn.classList.toggle("cursor-not-allowed", currentPage <= 1);
    }
    if (nextBtn) {
      nextBtn.disabled = currentPage >= totalPages;
      nextBtn.classList.toggle("opacity-50", currentPage >= totalPages);
      nextBtn.classList.toggle("cursor-not-allowed", currentPage >= totalPages);
    }
  }
}

function changeLessonsPage(delta) {
  const totalPages = Math.max(1, Math.ceil(loadedLessons.length / PAGE_SIZE));
  const newPage = currentPage + delta;
  if (newPage < 1 || newPage > totalPages) return;
  currentPage = newPage;
  renderLessonsPage();
}

function showLessonDetails(id) {
  const lesson = loadedLessons.find((l) => l.id == id);
  if (!lesson) return;

  const date = new Date(lesson.date);
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = String(date.getFullYear());
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  const dateStr = `${day}/${month}/${year} ore ${hours}:${minutes}`;

  document.getElementById("modalLessonTopic").textContent =
    lesson.topic || "Da definire";
  document.getElementById("modalLessonDate").textContent = dateStr;
  document.getElementById("modalLessonDuration").textContent =
    lesson.duration + " minuti";
  document.getElementById("modalLessonType").textContent =
    lesson.lesson_type === "package"
      ? "Inclusa in un pacchetto ore"
      : "Lezione singola";

  let statusText =
    lesson.status === "scheduled"
      ? "Programmata"
      : lesson.status === "completed"
        ? "Completata"
        : "Cancellata";
  document.getElementById("modalLessonStatus").textContent = statusText;

  const modeText = lesson.location_type === "online" ? "Online" : "In presenza";
  document.getElementById("modalLessonMode").textContent = modeText;

  const joinBtn = document.getElementById("modalJoinButton");
  if (joinBtn) {
    if (
      lesson.status === "scheduled" &&
      lesson.location_type === "online" &&
      lesson.meeting_link
    ) {
      joinBtn.classList.remove("hidden");
      joinBtn.href = lesson.meeting_link;
    } else {
      joinBtn.classList.add("hidden");
      joinBtn.removeAttribute("href");
    }
  }

  const modal = document.getElementById("lessonDetailsModal");
  if (!modal) return;
  modal.classList.remove("hidden");
  modal.classList.add("flex");
  modal.classList.add("overflow-y-auto");
  document.body.classList.add("overflow-hidden");
}

function closeLessonDetails() {
  const modal = document.getElementById("lessonDetailsModal");
  if (!modal) return;
  modal.classList.add("hidden");
  modal.classList.remove("flex");
  modal.classList.remove("overflow-y-auto");
  document.body.classList.remove("overflow-hidden");
}

// Event delegation for detail buttons and pagination controls

document.addEventListener("DOMContentLoaded", () => {
  loadStudentLessons();

  const list = document.getElementById("studentLessonsList");
  if (list) {
    list.addEventListener("click", (e) => {
      const btn = e.target.closest(".js-open-lesson");
      if (!btn) return;
      const id = btn.getAttribute("data-lesson-id");
      if (id) {
        showLessonDetails(id);
      }
    });
  }

  const paginationContainer = document.getElementById(
    "studentLessonsPagination",
  );
  if (paginationContainer) {
    const prevBtn = paginationContainer.querySelector(
      '[data-role="prev-page"]',
    );
    const nextBtn = paginationContainer.querySelector(
      '[data-role="next-page"]',
    );

    if (prevBtn) {
      prevBtn.addEventListener("click", () => changeLessonsPage(-1));
    }
    if (nextBtn) {
      nextBtn.addEventListener("click", () => changeLessonsPage(1));
    }
  }
});
