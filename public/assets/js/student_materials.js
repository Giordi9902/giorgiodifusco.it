async function loadStudentMaterials() {
  try {
    const response = await fetch(`${BASE_URL}/api/materials`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    if (!result.success) return;

    const list = document.getElementById("studentMaterialsList");
    if (!list) return;
    list.innerHTML = "";

    if (!result.data || result.data.length === 0) {
      const empty = document.createElement("div");
      empty.className =
        "text-center p-6 text-sm text-slate-500 border border-dashed border-slate-700 rounded-xl";
      empty.textContent = "Non ci sono ancora materiali disponibili.";
      list.appendChild(empty);
      return;
    }

    result.data.forEach((m) => {
      const item = document.createElement("div");
      item.className =
        "flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900 p-4";

      const uploaded = new Date(m.uploaded_at);
      const day = String(uploaded.getDate()).padStart(2, "0");
      const month = String(uploaded.getMonth() + 1).padStart(2, "0");
      const year = String(uploaded.getFullYear());
      const uploadedStr = `${day}/${month}/${year}`;
      const link = `${BASE_URL}/public${m.filepath}`;

      item.innerHTML = `
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-slate-100 mb-1">${m.filename}</span>
                    <span class="text-xs text-slate-400">Caricato il: ${uploadedStr}</span>
                </div>
                <div class="shrink-0 mt-2 sm:mt-0">
                    <a href="${link}" class="inline-flex items-center text-xs font-medium text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 px-4 py-2 rounded-full transition" target="_blank">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Scarica
                    </a>
                </div>
            `;
      list.appendChild(item);
    });
  } catch (error) {
    console.error("Error loading materials:", error);
  }
}

document.addEventListener("DOMContentLoaded", loadStudentMaterials);
