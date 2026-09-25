/**
 * Shared CMS editor — used by both blog_create.php and blog_edit.php.
 * Mode is determined at runtime by reading data attributes on #blogPostForm:
 *   data-post-id  → edit mode (PUT /api/blog/posts/{id})
 *   (absent)      → create mode (POST /api/blog/posts)
 */

document.addEventListener("DOMContentLoaded", () => {
  const form           = document.getElementById("blogPostForm");
  const textarea       = document.getElementById("blogContent");
  const preview        = document.getElementById("blogContentPreview");
  const courseSelect   = document.getElementById("blogCourseSelect");
  const subjectSelect  = document.getElementById("blogSubjectSelect");
  const imageInput     = document.getElementById("blogImageInput");
  const featuredInput  = document.getElementById("featuredImageId");

  const coverInput       = document.getElementById("coverImageInput");
  const coverPreviewImg  = document.getElementById("coverPreviewImg");
  const coverPlaceholder = document.getElementById("coverPreviewPlaceholder");
  const removeCoverBtn   = document.getElementById("removeCoverButton");

  if (!form || !textarea || !preview) return;

  const postId          = form.dataset.postId || null;
  const initialCourseId = courseSelect?.dataset.initialCourseId || null;
  const initialSubjectId = subjectSelect?.dataset.initialSubjectId || null;

  let pendingImageAlt = "";

  // ── Video URL parser ──────────────────────────────────────────────────────
  function parseVideoUrl(url) {
    let m;
    if ((m = url.match(/(?:youtube\.com\/watch\?(?:.*&)?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/))) {
      return `https://www.youtube-nocookie.com/embed/${m[1]}`;
    }
    if ((m = url.match(/vimeo\.com\/(\d+)/))) {
      return `https://player.vimeo.com/video/${m[1]}`;
    }
    return url;
  }

  function videoEmbedHtml(url) {
    const src = parseVideoUrl(url.trim());
    if (!src) return "";
    return `<div class="relative my-4 pb-[56.25%] h-0 overflow-hidden rounded-xl border border-slate-800">`
      + `<iframe class="absolute inset-0 w-full h-full rounded-xl" src="${src}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`
      + `</div>`;
  }

  // ── Inline formatting helpers ─────────────────────────────────────────────
  function applyInline(esc) {
    // images first (before links so ![ is not captured by [ )
    esc = esc.replace(/!\[([^\]]*)\]\(([^)]+)\)/g,
      '<img src="$2" alt="$1" class="my-3 rounded-xl border border-slate-800 max-w-full" loading="lazy">');
    esc = esc.replace(/\[([^\]]+)\]\(([^)]+)\)/g,
      '<a href="$2" class="text-indigo-300 underline underline-offset-2 hover:text-indigo-200" target="_blank" rel="noopener noreferrer">$1</a>');
    esc = esc.replace(/`([^`]+)`/g,
      '<code class="px-1.5 py-0.5 rounded bg-slate-950/70 border border-slate-800 text-[0.78rem] font-mono text-slate-100">$1</code>');
    esc = esc.replace(/\*\*(.+?)\*\*/g, '<strong class="font-semibold text-slate-50">$1</strong>');
    esc = esc.replace(/\*(.+?)\*/g,     '<em class="italic">$1</em>');
    return esc;
  }

  // ── Preview renderer ──────────────────────────────────────────────────────
  function renderPreview() {
    const lines       = (textarea.value || "").split(/\n/);
    let inCodeBlock   = false;
    let codeLang      = "";
    let inList        = false;
    let listTag       = "ul";
    const htmlLines   = [];

    for (const line of lines) {
      const trimmed = line.trim();

      // ``` code block
      if (trimmed.startsWith("```")) {
        if (!inCodeBlock) {
          inCodeBlock = true;
          codeLang    = trimmed.slice(3).trim();
          if (inList) { htmlLines.push(`</${listTag}>`); inList = false; }
          const langClass = codeLang ? ` language-${codeLang}` : "";
          htmlLines.push(`<pre class="mt-4 mb-4 rounded-xl bg-slate-950 border border-slate-800 p-4 overflow-x-auto text-xs text-slate-100"><code class="font-mono${langClass}">`);
        } else {
          inCodeBlock = false;
          htmlLines.push("</code></pre>");
        }
        continue;
      }

      if (inCodeBlock) {
        htmlLines.push(line.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;") + "\n");
        continue;
      }

      // @[video](url)
      const videoMatch = trimmed.match(/^@\[video\]\(([^)]+)\)$/);
      if (videoMatch) {
        if (inList) { htmlLines.push(`</${listTag}>`); inList = false; }
        htmlLines.push(videoEmbedHtml(videoMatch[1]));
        continue;
      }

      // --- hr
      if (trimmed === "---") {
        if (inList) { htmlLines.push(`</${listTag}>`); inList = false; }
        htmlLines.push('<hr class="my-6 border-slate-800">');
        continue;
      }

      // > blockquote
      if (trimmed.startsWith("> ")) {
        if (inList) { htmlLines.push(`</${listTag}>`); inList = false; }
        const inner = applyInline(trimmed.slice(2).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;"));
        htmlLines.push(`<blockquote class="my-3 border-l-4 border-indigo-500/60 pl-4 text-slate-300 italic text-sm">${inner}</blockquote>`);
        continue;
      }

      // Lists
      const isBullet   = /^[-*]\s+/.test(trimmed);
      const isNumbered = /^\d+\.\s+/.test(trimmed);

      if (isBullet || isNumbered) {
        const currentTag  = isNumbered ? "ol" : "ul";
        const itemRaw     = trimmed.replace(isNumbered ? /^\d+\.\s+/ : /^[-*]\s+/, "");
        const itemEsc     = applyInline(itemRaw.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;"));

        if (!inList || listTag !== currentTag) {
          if (inList) htmlLines.push(`</${listTag}>`);
          listTag = currentTag;
          htmlLines.push(listTag === "ol"
            ? '<ol class="list-decimal list-outside ml-5 mt-2 mb-3 space-y-1 text-sm text-slate-300">'
            : '<ul class="list-disc list-outside ml-5 mt-2 mb-3 space-y-1 text-sm text-slate-300">');
          inList = true;
        }
        htmlLines.push(`<li>${itemEsc}</li>`);
        continue;
      }

      if (trimmed === "") {
        if (inList) { htmlLines.push(`</${listTag}>`); inList = false; }
        htmlLines.push("<br>");
        continue;
      }

      if (inList) { htmlLines.push(`</${listTag}>`); inList = false; }

      let esc = line.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");

      if (esc.startsWith("### ")) {
        htmlLines.push(`<h3 class="text-sm sm:text-base font-semibold text-slate-100 mt-6 mb-2">${esc.slice(4).trim()}</h3>`);
        continue;
      }
      if (esc.startsWith("## ")) {
        htmlLines.push(`<h2 class="text-lg sm:text-xl font-semibold text-slate-50 mt-8 mb-2">${esc.slice(3).trim()}</h2>`);
        continue;
      }

      htmlLines.push(`<p class="text-sm text-slate-300 leading-relaxed">${applyInline(esc)}</p>`);
    }

    if (inList) htmlLines.push(`</${listTag}>`);

    preview.innerHTML = htmlLines.join("\n");

    if (window.renderMathInElement) {
      window.renderMathInElement(preview, {
        delimiters: [
          { left: "$$", right: "$$", display: true },
          { left: "$",  right: "$",  display: false },
          { left: "\\(", right: "\\)", display: false },
          { left: "\\[", right: "\\]", display: true },
        ],
      });
    }
    if (window.hljs) {
      preview.querySelectorAll("pre code").forEach(el => window.hljs.highlightElement(el));
    }
  }

  // ── Cursor helpers ────────────────────────────────────────────────────────
  function insertAtCursor(field, text) {
    const start  = field.selectionStart ?? field.value.length;
    const end    = field.selectionEnd ?? field.value.length;
    field.value  = field.value.substring(0, start) + text + field.value.substring(end);
    const pos    = start + text.length;
    field.selectionStart = field.selectionEnd = pos;
    field.focus();
    renderPreview();
  }

  function wrapSelection(field, before, after, placeholder = "testo") {
    const start     = field.selectionStart ?? 0;
    const end       = field.selectionEnd ?? 0;
    const selection = field.value.substring(start, end) || placeholder;
    const newText   = before + selection + after;
    field.value     = field.value.substring(0, start) + newText + field.value.substring(end);
    field.selectionStart = start + before.length;
    field.selectionEnd   = start + before.length + selection.length;
    field.focus();
    renderPreview();
  }

  function prependToLine(field, prefix) {
    const start     = field.selectionStart ?? 0;
    const lineStart = field.value.lastIndexOf("\n", start - 1) + 1;
    const already   = field.value.substring(lineStart).startsWith(prefix);
    if (already) {
      field.value = field.value.substring(0, lineStart) + field.value.substring(lineStart + prefix.length);
      field.selectionStart = field.selectionEnd = Math.max(lineStart, start - prefix.length);
    } else {
      field.value = field.value.substring(0, lineStart) + prefix + field.value.substring(lineStart);
      field.selectionStart = field.selectionEnd = start + prefix.length;
    }
    field.focus();
    renderPreview();
  }

  // ── Toolbar ───────────────────────────────────────────────────────────────
  document.querySelectorAll("[data-editor-action]").forEach(btn => {
    btn.addEventListener("click", async (e) => {
      e.preventDefault();
      const action = btn.dataset.editorAction;
      switch (action) {
        case "bold":       wrapSelection(textarea, "**", "**"); break;
        case "italic":     wrapSelection(textarea, "*",  "*");  break;
        case "h2":         prependToLine(textarea, "## "); break;
        case "h3":         prependToLine(textarea, "### "); break;
        case "blockquote": prependToLine(textarea, "> "); break;
        case "hr":         insertAtCursor(textarea, "\n---\n"); break;
        case "code": {
          const lang = window.showAppPrompt
            ? await window.showAppPrompt("Linguaggio del codice (es. c, python, javascript, sql...)", {
                title: "Blocco di codice",
                placeholder: "es. c — lascia vuoto per nessuno",
                primaryText: "Inserisci",
              })
            : prompt("Linguaggio del codice (lascia vuoto per nessuno)", "");
          const langTag = (lang || "").trim();
          wrapSelection(textarea, `\n\`\`\`${langTag}\n`, "\n```\n", "codice");
          break;
        }
      }
    });
  });

  // ── Course / Subject selects ──────────────────────────────────────────────
  async function loadCourses(selectedId = null) {
    if (!courseSelect) return;
    try {
      const res    = await fetch(`${BASE_URL}/api/blog/courses`, { headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" } });
      const result = await res.json();
      courseSelect.innerHTML = '<option value="">Nessun corso</option>';
      if (result.success && Array.isArray(result.data)) {
        result.data.forEach(course => {
          const opt = new Option(course.name, course.id, false, String(course.id) === String(selectedId));
          courseSelect.appendChild(opt);
        });
      }
    } catch (e) { console.error("loadCourses", e); }
  }

  async function loadSubjects(courseId, selectedId = null) {
    if (!subjectSelect) return;
    subjectSelect.innerHTML = '<option value="">Nessuna materia</option>';
    if (!courseId) return;
    try {
      const res    = await fetch(`${BASE_URL}/api/blog/subjects?course_id=${encodeURIComponent(courseId)}`, { headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" } });
      const result = await res.json();
      if (result.success && Array.isArray(result.data)) {
        result.data.forEach(sub => {
          const opt = new Option(sub.name, sub.id, false, String(sub.id) === String(selectedId));
          subjectSelect.appendChild(opt);
        });
      }
    } catch (e) { console.error("loadSubjects", e); }
  }

  courseSelect?.addEventListener("change", () => loadSubjects(courseSelect.value || null, null));

  document.getElementById("createCourseButton")?.addEventListener("click", async () => {
    const name = window.showAppPrompt
      ? await window.showAppPrompt("Nome del nuovo corso", { title: "Nuovo corso", placeholder: "Es. Analisi 1, Geometria...", primaryText: "Crea" })
      : prompt("Nome del nuovo corso");
    if (!name) return;
    try {
      const res    = await fetch(`${BASE_URL}/api/blog/courses`, { method: "POST", headers: { "Content-Type": "application/json", Accept: "application/json", "X-Requested-With": "XMLHttpRequest" }, body: JSON.stringify({ name, csrf_token: CSRF_TOKEN }) });
      const result = await res.json();
      if (!result.success) { showAlert(result.message || "Errore"); return; }
      const last = result.data?.courses?.at(-1);
      await loadCourses(last?.id ?? null);
      if (last?.id) await loadSubjects(last.id, null);
    } catch (e) { showAlert("Errore di rete"); }
  });

  document.getElementById("createSubjectButton")?.addEventListener("click", async () => {
    const courseId = courseSelect?.value;
    if (!courseId) { showAlert("Seleziona prima un corso", "Attenzione"); return; }
    const name = window.showAppPrompt
      ? await window.showAppPrompt("Nome della nuova materia", { title: "Nuova materia", placeholder: "Es. Geometria, Probabilità...", primaryText: "Crea" })
      : prompt("Nome della nuova materia");
    if (!name) return;
    try {
      const res    = await fetch(`${BASE_URL}/api/blog/subjects`, { method: "POST", headers: { "Content-Type": "application/json", Accept: "application/json", "X-Requested-With": "XMLHttpRequest" }, body: JSON.stringify({ course_id: courseId, name, csrf_token: CSRF_TOKEN }) });
      const result = await res.json();
      if (!result.success) { showAlert(result.message || "Errore"); return; }
      const last = result.data?.subjects?.at(-1);
      await loadSubjects(courseId, last?.id ?? null);
    } catch (e) { showAlert("Errore di rete"); }
  });

  // ── Link insert ───────────────────────────────────────────────────────────
  document.getElementById("insertLinkButton")?.addEventListener("click", async () => {
    const linkText = window.showAppPrompt
      ? await window.showAppPrompt("Testo del link", { title: "Inserisci link", placeholder: "Testo visibile", primaryText: "Avanti" })
      : prompt("Testo del link");
    if (!linkText) return;
    const url = window.showAppPrompt
      ? await window.showAppPrompt("URL del link", { title: "URL", placeholder: "https://...", primaryText: "Inserisci" })
      : prompt("URL", "https://");
    if (!url) return;
    insertAtCursor(textarea, `[${linkText}](${url})`);
  });

  // ── Image upload ──────────────────────────────────────────────────────────
  document.getElementById("insertImageButton")?.addEventListener("click", async () => {
    pendingImageAlt = (window.showAppPrompt
      ? await window.showAppPrompt("Testo alternativo (alt) per l'immagine", { title: "Alt text", placeholder: "Es. Grafico normalizzazione", primaryText: "Avanti" })
      : prompt("Alt text immagine")) || "";
    imageInput?.click();
  });

  imageInput?.addEventListener("change", async () => {
    if (!imageInput.files?.length) return;
    const file     = imageInput.files[0];
    const formData = new FormData();
    formData.append("image", file);
    formData.append("alt",   pendingImageAlt);
    formData.append("csrf_token", CSRF_TOKEN);
    if (postId) formData.append("post_id", postId);

    try {
      const res    = await fetch(`${BASE_URL}/api/blog/images`, { method: "POST", body: formData });
      const result = await res.json();
      if (!result.success) { showAlert(result.message || "Errore upload", "Errore", "error"); return; }
      const { url: imgUrl } = result.data ?? {};
      if (imgUrl) insertAtCursor(textarea, `\n![${pendingImageAlt}](${imgUrl})\n`);
      showAlert("Immagine inserita nel contenuto", "Ok", "success");
    } catch (e) {
      showAlert("Errore di rete durante l'upload", "Errore", "error");
    } finally {
      imageInput.value = "";
      pendingImageAlt  = "";
    }
  });

  // ── Cover image (featured_image_id) — separata dalle immagini nel contenuto ─
  function setCoverPreview(url) {
    if (!coverPreviewImg) return;
    if (url) {
      coverPreviewImg.src = url;
      coverPreviewImg.classList.remove("hidden");
      coverPlaceholder?.classList.add("hidden");
      removeCoverBtn?.classList.remove("hidden");
    } else {
      coverPreviewImg.src = "";
      coverPreviewImg.classList.add("hidden");
      coverPlaceholder?.classList.remove("hidden");
      removeCoverBtn?.classList.add("hidden");
    }
  }

  document.getElementById("uploadCoverButton")?.addEventListener("click", () => {
    coverInput?.click();
  });

  coverInput?.addEventListener("change", async () => {
    if (!coverInput.files?.length) return;
    const file     = coverInput.files[0];
    const formData = new FormData();
    formData.append("image", file);
    formData.append("csrf_token", CSRF_TOKEN);
    if (postId) formData.append("post_id", postId);

    try {
      const res    = await fetch(`${BASE_URL}/api/blog/images`, { method: "POST", body: formData });
      const result = await res.json();
      if (!result.success) { showAlert(result.message || "Errore upload", "Errore", "error"); return; }
      const { id: imgId, url: imgUrl } = result.data ?? {};
      if (featuredInput && imgId) featuredInput.value = String(imgId);
      setCoverPreview(imgUrl || null);
      showAlert("Copertina aggiornata", "Ok", "success");
    } catch (e) {
      showAlert("Errore di rete durante l'upload", "Errore", "error");
    } finally {
      coverInput.value = "";
    }
  });

  removeCoverBtn?.addEventListener("click", () => {
    if (featuredInput) featuredInput.value = "";
    setCoverPreview(null);
  });

  // ── Video embed ───────────────────────────────────────────────────────────
  document.getElementById("insertVideoButton")?.addEventListener("click", async () => {
    const url = window.showAppPrompt
      ? await window.showAppPrompt("URL del video (YouTube o Vimeo)", { title: "Incorpora video", placeholder: "https://www.youtube.com/watch?v=...", primaryText: "Incorpora" })
      : prompt("URL del video (YouTube o Vimeo)", "https://");
    if (!url) return;
    insertAtCursor(textarea, `\n@[video](${url.trim()})\n`);
  });

  // ── Submit ────────────────────────────────────────────────────────────────
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data       = Object.fromEntries(new FormData(form).entries());
    delete data.id;
    data.csrf_token  = CSRF_TOKEN;

    const isEdit = Boolean(postId);
    const url    = isEdit ? `${BASE_URL}/api/blog/posts/${postId}` : `${BASE_URL}/api/blog/posts`;
    const method = isEdit ? "PUT" : "POST";

    try {
      const res    = await fetch(url, { method, headers: { "Content-Type": "application/json", Accept: "application/json", "X-Requested-With": "XMLHttpRequest" }, body: JSON.stringify(data) });
      const result = await res.json();
      if (result.success) {
        await showAlert(result.message || (isEdit ? "Articolo aggiornato" : "Articolo salvato"), isEdit ? "Aggiornato" : "Salvato", "success");
        window.location.href = `${BASE_URL}/admin/blog`;
      } else {
        showAlert(result.message || "Errore durante il salvataggio", "Errore", "error");
      }
    } catch (err) {
      console.error(err);
      showAlert("Errore di rete durante il salvataggio", "Errore di rete", "error");
    }
  });

  // ── Init ──────────────────────────────────────────────────────────────────
  textarea.addEventListener("input", renderPreview);
  renderPreview();

  (async () => {
    await loadCourses(initialCourseId);
    if (initialCourseId) await loadSubjects(initialCourseId, initialSubjectId);
  })();

  // ── Alert helper ─────────────────────────────────────────────────────────
  async function showAlert(msg, title = "Info", variant = "info") {
    if (window.showAppAlert) {
      await window.showAppAlert(msg, { title, variant });
    } else {
      alert(msg);
    }
  }
});
