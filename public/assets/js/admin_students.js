// Admin students page logic extracted from app/Views/admin/students.php

document.addEventListener("DOMContentLoaded", () => {
  if (typeof BASE_URL === "undefined") return;

  const addStudentForm = document.getElementById("addStudentForm");
  if (addStudentForm) {
    addStudentForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData.entries());
      data.csrf_token = typeof CSRF_TOKEN !== "undefined" ? CSRF_TOKEN : "";

      try {
        const response = await fetch(`${BASE_URL}/api/students`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(data),
        });

        const result = await response.json();
        if (result.success) {
          await (window.showAppAlert
            ? window.showAppAlert(
                result.message || "Studente salvato con successo",
                {
                  title: "Studente salvato",
                  variant: "success",
                },
              )
            : Promise.resolve(
                alert(result.message || "Studente salvato con successo"),
              ));
          const modal = document.getElementById("addStudentModal");
          if (modal) modal.classList.add("hidden");
          e.target.reset();
          loadStudents();
        } else {
          await (window.showAppAlert
            ? window.showAppAlert(
                result.message || "Errore durante il salvataggio",
                {
                  title: "Errore",
                  variant: "error",
                },
              )
            : Promise.resolve(
                alert(result.message || "Errore durante il salvataggio"),
              ));
        }
      } catch (error) {
        console.error("Error:", error);
        await (window.showAppAlert
          ? window.showAppAlert(
              "Errore di rete durante il salvataggio dello studente",
              {
                title: "Errore di rete",
                variant: "error",
              },
            )
          : Promise.resolve(
              alert("Errore di rete durante il salvataggio dello studente"),
            ));
      }
    });
  }

  loadStudents();
});

async function loadStudents() {
  try {
    const response = await fetch(`${BASE_URL}/api/students`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();

    if (result.success) {
      const list = document.getElementById("studentsList");
      if (!list) return;
      list.innerHTML = "";

      if (!result.data || result.data.length === 0) {
        const empty = document.createElement("div");
        empty.className =
          "text-center p-6 text-sm text-slate-500 border border-dashed border-slate-700 rounded-xl";
        empty.textContent = "Nessuno studente presente.";
        list.appendChild(empty);
        return;
      }

      result.data.forEach((student) => {
        const item = document.createElement("div");
        item.className =
          "flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900 p-4";

        const d = new Date(student.created_at);
        const day = String(d.getDate()).padStart(2, "0");
        const month = String(d.getMonth() + 1).padStart(2, "0");
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
                        <button data-action="delete" data-id="${student.id}" class="text-xs font-medium text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded-full transition">Elimina</button>
                    </div>
                `;
        list.appendChild(item);
      });

      list.addEventListener(
        "click",
        async (e) => {
          const target = e.target;
          if (!(target instanceof HTMLElement)) return;
          const action = target.getAttribute("data-action");
          const id = target.getAttribute("data-id");
          if (action === "delete" && id) {
            await deleteStudent(id);
          }
        },
        { once: true },
      );
    }
  } catch (error) {
    console.error("Error loading students:", error);
  }
}

async function deleteStudent(id) {
  const confirmed = await (window.showAppConfirm
    ? window.showAppConfirm("Sei sicuro di voler eliminare questo studente?", {
        title: "Elimina studente",
        primaryText: "Elimina",
        secondaryText: "Annulla",
        variant: "danger",
      })
    : Promise.resolve(
        confirm("Are you sure you want to delete this student?"),
      ));
  if (!confirmed) return;

  try {
    const response = await fetch(`${BASE_URL}/api/students/${id}`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        csrf_token: typeof CSRF_TOKEN !== "undefined" ? CSRF_TOKEN : "",
      }),
    });
    const result = await response.json();
    if (result.success) {
      await (window.showAppAlert
        ? window.showAppAlert(
            result.message || "Studente eliminato con successo",
            {
              title: "Studente eliminato",
              variant: "success",
            },
          )
        : Promise.resolve(
            alert(result.message || "Studente eliminato con successo"),
          ));
      loadStudents();
    } else {
      await (window.showAppAlert
        ? window.showAppAlert(
            result.message || "Errore durante l'eliminazione",
            {
              title: "Errore",
              variant: "error",
            },
          )
        : Promise.resolve(
            alert(result.message || "Errore durante l'eliminazione"),
          ));
    }
  } catch (error) {
    console.error("Error deleting student:", error);
    await (window.showAppAlert
      ? window.showAppAlert(
          "Errore di rete durante l'eliminazione dello studente",
          {
            title: "Errore di rete",
            variant: "error",
          },
        )
      : Promise.resolve(
          alert("Errore di rete durante l'eliminazione dello studente"),
        ));
  }
}
