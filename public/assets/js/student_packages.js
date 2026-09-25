async function loadStudentPackages() {
  try {
    const response = await fetch(`${BASE_URL}/api/packages`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    if (!result.success) return;

    const list = document.getElementById("studentPackagesList");
    if (!list) return;
    list.innerHTML = "";

    if (!result.data || result.data.length === 0) {
      const empty = document.createElement("div");
      empty.className =
        "text-center p-6 text-sm text-slate-500 border border-dashed border-slate-700 rounded-xl";
      empty.textContent = "Non hai ancora pacchetti attivi o completati.";
      list.appendChild(empty);
      return;
    }

    result.data.forEach((pkg) => {
      const item = document.createElement("div");
      item.className =
        "flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900 p-4";

      const created = new Date(pkg.created_at);
      const day = String(created.getDate()).padStart(2, "0");
      const month = String(created.getMonth() + 1).padStart(2, "0");
      const year = String(created.getFullYear());
      const createdStr = `${day}/${month}/${year}`;

      let statusBadgeClasses = "bg-slate-500/10 text-slate-300";
      let statusText = pkg.status;
      if (pkg.status === "active") {
        statusBadgeClasses =
          "bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20";
        statusText = "Attivo";
      } else if (pkg.status === "completed") {
        statusBadgeClasses =
          "bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20";
        statusText = "Completato";
      }

      const percentUsed =
        pkg.total_minutes > 0
          ? (pkg.used_minutes / pkg.total_minutes) * 100
          : 0;

      item.innerHTML = `
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-slate-100">${createdStr}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ${statusBadgeClasses}">
                            ${statusText}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mb-2">Descrizione: <span class="text-slate-300">${pkg.description || "Nessuna"}</span></p>

                    <div class="w-full bg-slate-800 rounded-full h-1.5 mb-1 relative overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: ${percentUsed}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                        <span>Usati: ${pkg.used_minutes} min</span>
                        <span>Totali: ${pkg.total_minutes} min</span>
                    </div>
                </div>
            `;
      list.appendChild(item);
    });
  } catch (error) {
    console.error("Error loading packages:", error);
  }
}

document.addEventListener("DOMContentLoaded", loadStudentPackages);
