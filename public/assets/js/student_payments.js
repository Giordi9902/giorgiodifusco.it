async function loadStudentPayments() {
  try {
    const response = await fetch(`${BASE_URL}/api/payments`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();
    if (!result.success) return;

    const list = document.getElementById("studentPaymentsList");
    if (!list) return;
    list.innerHTML = "";

    if (!result.data || result.data.length === 0) {
      const empty = document.createElement("div");
      empty.className =
        "text-center p-6 text-sm text-slate-500 border border-dashed border-slate-700 rounded-xl";
      empty.textContent = "Non risultano pagamenti registrati.";
      list.appendChild(empty);
      return;
    }

    result.data.forEach((p) => {
      const item = document.createElement("div");
      item.className =
        "flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900 p-4";

      const d = new Date(p.date);
      const day = String(d.getDate()).padStart(2, "0");
      const month = String(d.getMonth() + 1).padStart(2, "0");
      const year = String(d.getFullYear());
      const dateStr = `${day}/${month}/${year}`;

      item.innerHTML = `
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-slate-100">${dateStr}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-slate-500/10 text-slate-300">
                            Metodo: ${p.method || "N/A"}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Note: ${p.notes || "Nessuna nota"}</p>
                </div>
                <div class="shrink-0 mt-2 sm:mt-0">
                     <span class="text-lg font-bold text-emerald-400 tracking-tight">${parseFloat(p.amount).toFixed(2)} €</span>
                </div>
            `;
      list.appendChild(item);
    });
  } catch (error) {
    console.error("Error loading payments:", error);
  }
}

document.addEventListener("DOMContentLoaded", loadStudentPayments);
