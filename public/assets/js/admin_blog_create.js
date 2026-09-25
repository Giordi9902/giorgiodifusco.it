// Blog create page logic extracted from app/Views/admin/blog_create.php

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("blogPostForm");
  const textarea = document.getElementById("blogContent");
  const preview = document.getElementById("blogContentPreview");
  const courseSelect = document.getElementById("blogCourseSelect");
  const subjectSelect = document.getElementById("blogSubjectSelect");
  const createCourseButton = document.getElementById("createCourseButton");
  const createSubjectButton = document.getElementById("createSubjectButton");
  const insertLinkButton = document.getElementById("insertLinkButton");
  const insertImageButton = document.getElementById("insertImageButton");
  const imageInput = document.getElementById("blogImageInput");
  const featuredImageInput = document.getElementById("featuredImageId");

  let pendingImageAlt = "";

  async function loadCourses(selectedId = null) {
    if (!courseSelect || typeof BASE_URL === "undefined") return;
    try {
      const response = await fetch(`${BASE_URL}/api/blog/courses`, {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      });
      const result = await response.json();
      courseSelect.innerHTML = '<option value="">Nessun corso</option>';
      if (result.success && Array.isArray(result.data)) {
        result.data.forEach((course) => {
          const option = document.createElement("option");
          option.value = course.id;
          option.textContent = course.name;
          if (selectedId && String(course.id) === String(selectedId)) {
            option.selected = true;
          }
          courseSelect.appendChild(option);
        });
      }
    } catch (e) {
      console.error("Errore nel caricamento dei corsi", e);
    }
  }

  async function loadSubjects(courseId, selectedId = null) {
    if (!subjectSelect || typeof BASE_URL === "undefined") return;
    subjectSelect.innerHTML = '<option value="">Nessuna materia</option>';
    if (!courseId) {
      return;
    }
    try {
      const response = await fetch(
        `${BASE_URL}/api/blog/subjects?course_id=${encodeURIComponent(courseId)}`,
        {
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        },
      );
      const result = await response.json();
      if (result.success && Array.isArray(result.data)) {
        result.data.forEach((subject) => {
          const option = document.createElement("option");
          option.value = subject.id;
          option.textContent = subject.name;
          if (selectedId && String(subject.id) === String(selectedId)) {
            option.selected = true;
          }
          subjectSelect.appendChild(option);
        });
      }
    } catch (e) {
      console.error("Errore nel caricamento delle materie", e);
    }
  }

  function renderPreview() {
    if (!preview || !textarea) return;
    const raw = textarea.value || "";

    const lines = raw.split(/\n/);
    let inCodeBlock = false;
    let codeLang = "";
    let inList = false;
    let listTag = "ul";

    const htmlLines = [];

    for (let line of lines) {
      const trimmed = line.trim();

      // Blocchi di codice ```lang
      if (trimmed.startsWith("```")) {
        if (!inCodeBlock) {
          inCodeBlock = true;
          codeLang = trimmed.slice(3).trim();
          const langClass = codeLang ? ` language-${codeLang}` : "";
          if (inList) {
            htmlLines.push(`</${listTag}>`);
            inList = false;
          }
          htmlLines.push(
            `<pre class="mt-3 mb-3 rounded-lg bg-slate-950/80 border border-slate-800 p-3 overflow-x-auto text-xs text-slate-100"><code class="font-mono${langClass}">`,
          );
        } else {
          inCodeBlock = false;
          codeLang = "";
          htmlLines.push("</code></pre>");
        }
        continue;
      }

      if (inCodeBlock) {
        const escCode = line
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;");
        htmlLines.push(escCode + "<br>");
        continue;
      }

      // Gestione elenchi puntati e numerati
      const isBullet = /^[-*]\s+/.test(trimmed);
      const isNumbered = /^\d+\.\s+/.test(trimmed);

      if (isBullet || isNumbered) {
        const currentTag = isNumbered ? "ol" : "ul";
        let itemTextRaw = trimmed.replace(
          isNumbered ? /^\d+\.\s+/ : /^[-*]\s+/,
          "",
        );

        let itemEsc = itemTextRaw
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;");

        // Link [testo](url)
        itemEsc = itemEsc.replace(
          /\[([^\]]+)]\(([^)]+)\)/g,
          '<a href="$2" class="text-indigo-300 underline underline-offset-2 hover:text-indigo-200" target="_blank" rel="noopener noreferrer">$1</a>',
        );

        // Immagini ![alt](url)
        itemEsc = itemEsc.replace(
          /!\[([^\]]*)]\(([^)]+)\)/g,
          '<img src="$2" alt="$1" class="my-3 rounded-lg border border-slate-800 max-w-full">',
        );

        // inline code
        itemEsc = itemEsc.replace(
          /`([^`]+)`/g,
          '<code class="px-1 rounded bg-slate-950/70 border border-slate-800 text-[0.78rem] font-mono text-slate-100">$1</code>',
        );
        // grassetto **
        itemEsc = itemEsc.replace(
          /\*\*(.+?)\*\*/g,
          '<strong class="font-semibold">$1<\/strong>',
        );
        // corsivo *
        itemEsc = itemEsc.replace(/\*(.+?)\*/g, '<em class="italic">$1<\/em>');

        if (!inList || listTag !== currentTag) {
          if (inList) {
            htmlLines.push(`</${listTag}>`);
          }
          listTag = currentTag;
          htmlLines.push(
            listTag === "ol"
              ? '<ol class="list-decimal list-inside mt-1 mb-2 space-y-1 text-sm">'
              : '<ul class="list-disc list-inside mt-1 mb-2 space-y-1 text-sm">',
          );
          inList = true;
        }

        htmlLines.push(`<li>${itemEsc}</li>`);
        continue;
      }

      // Riga vuota chiude eventuale lista
      if (trimmed === "") {
        if (inList) {
          htmlLines.push(`</${listTag}>`);
          inList = false;
        }
        htmlLines.push("<br>");
        continue;
      }

      if (inList) {
        htmlLines.push(`</${listTag}>`);
        inList = false;
      }

      let esc = line
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

      // Titoli
      if (esc.startsWith("### ")) {
        const text = esc.slice(4).trim();
        htmlLines.push(
          `<h3 class="text-sm sm:text-base font-semibold text-slate-100 mt-4 mb-1">${text}</h3>`,
        );
        continue;
      }
      if (esc.startsWith("## ")) {
        const text = esc.slice(3).trim();
        htmlLines.push(
          `<h2 class="text-base sm:text-lg font-semibold text-slate-100 mt-4 mb-1">${text}</h2>`,
        );
        continue;
      }

      // Link [testo](url)
      esc = esc.replace(
        /\[([^\]]+)]\(([^)]+)\)/g,
        '<a href="$2" class="text-indigo-300 underline underline-offset-2 hover:text-indigo-200" target="_blank" rel="noopener noreferrer">$1</a>',
      );

      // Immagini ![alt](url)
      esc = esc.replace(
        /!\[([^\]]*)]\(([^)]+)\)/g,
        '<img src="$2" alt="$1" class="my-3 rounded-lg border border-slate-800 max-w-full">',
      );

      // inline code
      esc = esc.replace(
        /`([^`]+)`/g,
        '<code class="px-1 rounded bg-slate-950/70 border border-slate-800 text-[0.78rem] font-mono text-slate-100">$1</code>',
      );
      // grassetto **
      esc = esc.replace(
        /\*\*(.+?)\*\*/g,
        '<strong class="font-semibold">$1<\/strong>',
      );
      // corsivo *
      esc = esc.replace(/\*(.+?)\*/g, '<em class="italic">$1<\/em>');

      htmlLines.push(`<span>${esc}</span><br>`);
    }

    if (inList) {
      htmlLines.push(`</${listTag}>`);
    }

    preview.innerHTML = htmlLines.join("\n");

    if (window.renderMathInElement) {
      window.renderMathInElement(preview, {
        delimiters: [
          { left: "$$", right: "$$", display: true },
          { left: "\$", right: "\$", display: false },
          { left: "\\(", right: "\\)", display: false },
          { left: "\\[", right: "\\]", display: true },
        ],
      });
    }

    if (window.hljs) {
      preview
        .querySelectorAll("pre code")
        .forEach((el) => window.hljs.highlightElement(el));
    }
  }

  function insertAtCursor(field, text) {
    if (!field) return;
    const start = field.selectionStart ?? field.value.length;
    const end = field.selectionEnd ?? field.value.length;
    const before = field.value.substring(0, start);
    const after = field.value.substring(end);
    field.value = before + text + after;
    const newPos = before.length + text.length;
    field.selectionStart = field.selectionEnd = newPos;
    field.focus();
    renderPreview();
  }

  if (courseSelect) {
    courseSelect.addEventListener("change", () => {
      const courseId = courseSelect.value || null;
      loadSubjects(courseId, null);
    });
  }

  if (createCourseButton) {
    createCourseButton.addEventListener("click", async () => {
      let name = null;
      if (window.showAppPrompt) {
        name = await window.showAppPrompt("Nome del nuovo corso", {
          title: "Nuovo corso",
          placeholder: "Es. Algebra, Analisi 1...",
          primaryText: "Crea corso",
        });
      } else {
        name = prompt("Nome del nuovo corso");
      }
      if (!name || typeof BASE_URL === "undefined") return;
      try {
        const response = await fetch(`${BASE_URL}/api/blog/courses`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            name: name,
            csrf_token: typeof CSRF_TOKEN !== "undefined" ? CSRF_TOKEN : "",
          }),
        });
        const result = await response.json();
        if (!result.success) {
          await (window.showAppAlert
            ? window.showAppAlert(
                result.message || "Errore durante la creazione del corso",
                {
                  title: "Errore",
                  variant: "error",
                },
              )
            : Promise.resolve(
                alert(
                  result.message || "Errore durante la creazione del corso",
                ),
              ));
          return;
        }
        let newCourseId = null;
        if (result.data && Array.isArray(result.data.courses)) {
          const last = result.data.courses[result.data.courses.length - 1];
          newCourseId = last ? last.id : null;
        }
        await loadCourses(newCourseId);
        if (newCourseId) {
          await loadSubjects(newCourseId, null);
        }
      } catch (e) {
        console.error("Errore nella creazione del corso", e);
        await (window.showAppAlert
          ? window.showAppAlert(
              "Errore di rete durante la creazione del corso",
              {
                title: "Errore di rete",
                variant: "error",
              },
            )
          : Promise.resolve(
              alert("Errore di rete durante la creazione del corso"),
            ));
      }
    });
  }

  if (insertLinkButton && textarea) {
    insertLinkButton.addEventListener("click", async () => {
      let linkText = "";
      if (window.showAppPrompt) {
        linkText = await window.showAppPrompt(
          "Testo del link (es. Vedi anche ...)",
          {
            title: "Inserisci link",
            placeholder: "Testo visibile del link",
            primaryText: "Avanti",
          },
        );
      } else {
        linkText = prompt("Testo del link") || "";
      }
      if (!linkText) return;

      let url = "";
      if (window.showAppPrompt) {
        url = await window.showAppPrompt(
          "URL del link (puoi usare /blog/slug-articolo)",
          {
            title: "URL del link",
            placeholder: `${BASE_URL || ""}/blog/... oppure /blog/slug-articolo`,
            primaryText: "Inserisci",
          },
        );
      } else {
        url = prompt(
          "URL del link (puoi usare /blog/slug-articolo)",
          "https://",
        );
      }
      if (!url) return;

      const snippet = `[${linkText}](${url})`;
      insertAtCursor(textarea, snippet);
    });
  }

  if (insertImageButton && imageInput && textarea) {
    insertImageButton.addEventListener("click", async () => {
      pendingImageAlt = "";
      if (window.showAppPrompt) {
        pendingImageAlt =
          (await window.showAppPrompt(
            "Testo alternativo per l'immagine (per accessibilità e SEO)",
            {
              title: "Alt text immagine",
              placeholder: "Es. Studente che studia matematica",
              primaryText: "Avanti",
            },
          )) || "";
      } else {
        pendingImageAlt =
          prompt(
            "Testo alternativo per l'immagine (per accessibilità e SEO)",
          ) || "";
      }

      imageInput.click();
    });

    imageInput.addEventListener("change", async () => {
      if (!imageInput.files || imageInput.files.length === 0) {
        return;
      }
      if (typeof BASE_URL === "undefined") return;

      const file = imageInput.files[0];
      const formData = new FormData();
      formData.append("image", file);
      formData.append("alt", pendingImageAlt || "");
      formData.append(
        "csrf_token",
        typeof CSRF_TOKEN !== "undefined" ? CSRF_TOKEN : "",
      );

      try {
        const response = await fetch(`${BASE_URL}/api/blog/images`, {
          method: "POST",
          body: formData,
        });
        const result = await response.json();
        if (!result.success) {
          await (window.showAppAlert
            ? window.showAppAlert(
                result.message || "Errore durante il caricamento dell'immagine",
                {
                  title: "Errore",
                  variant: "error",
                },
              )
            : Promise.resolve(
                alert(
                  result.message ||
                    "Errore durante il caricamento dell'immagine",
                ),
              ));
          return;
        }

        const imageId = result.data && result.data.id ? result.data.id : null;
        const imageUrl = result.data && result.data.url ? result.data.url : "";

        if (featuredImageInput && imageId) {
          featuredImageInput.value = String(imageId);
        }

        if (imageUrl) {
          const snippet = `![${pendingImageAlt || ""}](${imageUrl})`;
          insertAtCursor(textarea, `\n${snippet}\n`);
        }

        imageInput.value = "";
        pendingImageAlt = "";

        await (window.showAppAlert
          ? window.showAppAlert("Immagine caricata con successo", {
              title: "Immagine caricata",
              variant: "success",
            })
          : Promise.resolve(alert("Immagine caricata con successo")));
      } catch (e) {
        console.error("Errore upload immagine", e);
        await (window.showAppAlert
          ? window.showAppAlert(
              "Errore di rete durante il caricamento dell'immagine",
              {
                title: "Errore di rete",
                variant: "error",
              },
            )
          : Promise.resolve(
              alert("Errore di rete durante il caricamento dell'immagine"),
            ));
      }
    });
  }

  if (createSubjectButton) {
    createSubjectButton.addEventListener("click", async () => {
      const courseId = courseSelect ? courseSelect.value : "";
      if (!courseId) {
        await (window.showAppAlert
          ? window.showAppAlert("Seleziona prima un corso", {
              title: "Attenzione",
            })
          : Promise.resolve(alert("Seleziona prima un corso")));
        return;
      }
      let name = null;
      if (window.showAppPrompt) {
        name = await window.showAppPrompt(
          "Nome della nuova materia per questo corso",
          {
            title: "Nuova materia",
            placeholder: "Es. Geometria, Probabilità...",
            primaryText: "Crea materia",
          },
        );
      } else {
        name = prompt("Nome della nuova materia per questo corso");
      }
      if (!name || typeof BASE_URL === "undefined") return;
      try {
        const response = await fetch(`${BASE_URL}/api/blog/subjects`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            course_id: courseId,
            name: name,
            csrf_token: typeof CSRF_TOKEN !== "undefined" ? CSRF_TOKEN : "",
          }),
        });
        const result = await response.json();
        if (!result.success) {
          await (window.showAppAlert
            ? window.showAppAlert(
                result.message || "Errore durante la creazione della materia",
                {
                  title: "Errore",
                  variant: "error",
                },
              )
            : Promise.resolve(
                alert(
                  result.message || "Errore durante la creazione della materia",
                ),
              ));
          return;
        }
        let newSubjectId = null;
        if (result.data && Array.isArray(result.data.subjects)) {
          const last = result.data.subjects[result.data.subjects.length - 1];
          newSubjectId = last ? last.id : null;
        }
        await loadSubjects(courseId, newSubjectId);
      } catch (e) {
        console.error("Errore nella creazione della materia", e);
        await (window.showAppAlert
          ? window.showAppAlert(
              "Errore di rete durante la creazione della materia",
              {
                title: "Errore di rete",
                variant: "error",
              },
            )
          : Promise.resolve(
              alert("Errore di rete durante la creazione della materia"),
            ));
      }
    });
  }

  if (textarea) {
    textarea.addEventListener("input", () => {
      renderPreview();
    });
    renderPreview();
  }

  loadCourses(null);

  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (typeof BASE_URL === "undefined") return;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());
      data.csrf_token = typeof CSRF_TOKEN !== "undefined" ? CSRF_TOKEN : "";

      try {
        const response = await fetch(`${BASE_URL}/api/blog/posts`, {
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
                result.message || "Articolo salvato con successo",
                {
                  title: "Articolo salvato",
                  variant: "success",
                },
              )
            : Promise.resolve(
                alert(result.message || "Articolo salvato con successo"),
              ));
          window.location.href = `${BASE_URL}/admin/blog`;
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
        console.error("Error saving blog post:", error);
        await (window.showAppAlert
          ? window.showAppAlert(
              "Errore di rete durante il salvataggio dell'articolo",
              {
                title: "Errore di rete",
                variant: "error",
              },
            )
          : Promise.resolve(
              alert("Errore di rete durante il salvataggio dell'articolo"),
            ));
      }
    });
  }
});
