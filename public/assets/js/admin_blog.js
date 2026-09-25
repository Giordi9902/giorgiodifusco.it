// Blog admin list page logic extracted from app/Views/admin/blog.php

document.addEventListener("DOMContentLoaded", () => {
  if (typeof BASE_URL === "undefined") return;
  loadBlogPosts();
});

async function loadBlogPosts() {
  try {
    const response = await fetch(`${BASE_URL}/api/blog/posts`, {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    });
    const result = await response.json();

    if (!result.success) return;

    const list = document.getElementById("blogPostsList");
    if (!list) return;

    list.innerHTML = "";
    if (!result.data || result.data.length === 0) {
      const li = document.createElement("li");
      li.className = "text-gray-500 text-sm p-4";
      li.textContent = "Nessun articolo ancora presente.";
      list.appendChild(li);
      return;
    }

    result.data.forEach((post) => {
      const li = document.createElement("li");
      li.className =
        "flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/60 p-4 transition hover:-translate-y-0.5 hover:border-indigo-500/50 hover:bg-slate-900";

      const left = document.createElement("div");
      left.className = "flex flex-col";

      const title = document.createElement("span");
      title.className =
        "font-semibold text-slate-100 mb-0.5 text-base hover:text-indigo-400 cursor-pointer transition";
      title.textContent = post.title;

      const meta = document.createElement("span");
      meta.className = "text-xs text-slate-500 flex items-center gap-2";
      const date = post.published_at || post.created_at;

      meta.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                ${post.status === "published" ? "Pubblicato il " : "Bozza crata il "}${
                  date
                    ? (() => {
                        const d = new Date(date);
                        const day = String(d.getDate()).padStart(2, "0");
                        const month = String(d.getMonth() + 1).padStart(2, "0");
                        const year = String(d.getFullYear());
                        return `${day}/${month}/${year}`;
                      })()
                    : ""
                }`;

      left.appendChild(title);
      left.appendChild(meta);

      const right = document.createElement("div");
      right.className = "flex items-center gap-3 mt-3 sm:mt-0";

      const badge = document.createElement("span");
      badge.className =
        post.status === "published"
          ? "inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-400 ring-1 ring-inset ring-emerald-500/20"
          : "inline-flex items-center rounded-full bg-slate-500/10 px-2.5 py-1 text-xs font-medium text-slate-400 ring-1 ring-inset ring-slate-500/20";
      badge.textContent = post.status === "published" ? "Pubblicato" : "Bozza";

      const actions = document.createElement("div");
      actions.className =
        "flex items-center gap-2 border-l border-slate-700 pl-3";
      actions.innerHTML = `
                <button class="text-indigo-400 hover:text-indigo-300 transition text-sm font-medium" data-action="edit" data-id="${post.id}">Modifica</button>
                <button class="text-red-400 hover:text-red-300 transition text-sm font-medium" data-action="delete" data-id="${post.id}">Elimina</button>
            `;

      right.appendChild(badge);
      right.appendChild(actions);

      li.appendChild(left);
      li.appendChild(right);

      list.appendChild(li);
    });

    // Delegate clicks for edit/delete
    list.addEventListener(
      "click",
      (e) => {
        const target = e.target;
        if (!(target instanceof HTMLElement)) return;
        const action = target.getAttribute("data-action");
        const id = target.getAttribute("data-id");
        if (!id) return;
        if (action === "edit") {
          editPost(id);
        } else if (action === "delete") {
          deletePost(id);
        }
      },
      { once: true },
    );
  } catch (error) {
    console.error("Error loading blog posts:", error);
  }
}

function editPost(id) {
  if (!id) return;
  window.location.href = `${BASE_URL}/cms/blog/edit/${id}`;
}

async function deletePost(id) {
  if (!id) return;
  const confirmed = await (window.showAppConfirm
    ? window.showAppConfirm("Sei sicuro di voler eliminare questo articolo?", {
        title: "Elimina articolo",
        primaryText: "Elimina",
        secondaryText: "Annulla",
        variant: "danger",
      })
    : Promise.resolve(
        confirm("Sei sicuro di voler eliminare questo articolo?"),
      ));
  if (!confirmed) return;

  try {
    const response = await fetch(`${BASE_URL}/api/blog/posts/${id}`, {
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
            result.message || "Articolo eliminato con successo",
            {
              title: "Articolo eliminato",
              variant: "success",
            },
          )
        : Promise.resolve(
            alert(result.message || "Articolo eliminato con successo"),
          ));
      loadBlogPosts();
    } else {
      await (window.showAppAlert
        ? window.showAppAlert(
            result.message || "Errore durante l'eliminazione dell'articolo",
            {
              title: "Errore",
              variant: "error",
            },
          )
        : Promise.resolve(
            alert(
              result.message || "Errore durante l'eliminazione dell'articolo",
            ),
          ));
    }
  } catch (error) {
    console.error("Error deleting blog post:", error);
    await (window.showAppAlert
      ? window.showAppAlert(
          "Errore di rete durante l'eliminazione dell'articolo",
          {
            title: "Errore di rete",
            variant: "error",
          },
        )
      : Promise.resolve(
          alert("Errore di rete durante l'eliminazione dell'articolo"),
        ));
  }
}
