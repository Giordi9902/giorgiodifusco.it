/**
 * Editor articoli del CMS (TinyMCE) — usato da blog_create.php e blog_edit.php
 * tramite il form condiviso cms/_blog_form.php.
 * La modalità si legge dal form #blogPostForm:
 *   data-post-id  → modifica (PUT /api/blog/posts/{id})
 *   (assente)     → nuovo articolo (POST /api/blog/posts)
 */

document.addEventListener("DOMContentLoaded", () => {
  const form          = document.getElementById("blogPostForm");
  const textarea      = document.getElementById("blogContent");
  const courseSelect  = document.getElementById("blogCourseSelect");
  const subjectSelect = document.getElementById("blogSubjectSelect");
  const taxonomyError = document.getElementById("taxonomyError");
  const submitButton  = document.getElementById("blogSubmitButton");
  const featuredInput = document.getElementById("featuredImageId");

  const coverInput       = document.getElementById("coverImageInput");
  const coverPreviewImg  = document.getElementById("coverPreviewImg");
  const coverPlaceholder = document.getElementById("coverPreviewPlaceholder");
  const removeCoverBtn   = document.getElementById("removeCoverButton");

  if (!form || !textarea) return;

  const postId           = form.dataset.postId || null;
  const initialCourseId  = courseSelect?.dataset.initialCourseId || null;
  const initialSubjectId = subjectSelect?.dataset.initialSubjectId || null;
  const jsonHeaders      = { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" };

  let isDirty = false;

  // ── Helpers ───────────────────────────────────────────────────────────────
  async function showAlert(msg, title = "Info", variant = "info") {
    if (window.showAppAlert) {
      await window.showAppAlert(msg, { title, variant });
    } else {
      alert(msg);
    }
  }

  async function askText(message, options = {}) {
    return window.showAppPrompt ? window.showAppPrompt(message, options) : prompt(message);
  }

  /**
   * fetch + JSON con messaggi d'errore leggibili: se la risposta non è JSON
   * (sessione scaduta, pagina d'errore del server...) lo dice invece di fallire in silenzio.
   */
  async function apiRequest(url, options = {}) {
    const res = await fetch(url, { credentials: "same-origin", ...options, headers: { ...jsonHeaders, ...(options.headers || {}) } });
    let result;
    try {
      result = await res.json();
    } catch {
      throw new Error(res.redirected || res.status === 401
        ? "Sessione scaduta: effettua di nuovo l'accesso."
        : `Risposta non valida dal server (HTTP ${res.status}).`);
    }
    if (!res.ok || !result.success) {
      throw new Error(result.message || `Errore HTTP ${res.status}`);
    }
    return result;
  }

  async function uploadImage(file, onProgress) {
    const formData = new FormData();
    formData.append("image", file, file.name || "immagine.png");
    formData.append("csrf_token", CSRF_TOKEN);
    if (postId) formData.append("post_id", postId);

    // XHR invece di fetch per avere l'avanzamento dell'upload nell'editor
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", `${BASE_URL}/api/blog/images`);
      xhr.withCredentials = true;
      xhr.setRequestHeader("Accept", "application/json");
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      xhr.upload.onprogress = (e) => { if (e.lengthComputable && onProgress) onProgress((e.loaded / e.total) * 100); };
      xhr.onerror = () => reject(new Error("Errore di rete durante l'upload"));
      xhr.onload = () => {
        let result;
        try { result = JSON.parse(xhr.responseText); } catch { result = null; }
        if (xhr.status >= 200 && xhr.status < 300 && result?.success) {
          resolve(result.data);
        } else {
          reject(new Error(result?.message || `Upload non riuscito (HTTP ${xhr.status})`));
        }
      };
      xhr.send(formData);
    });
  }

  // ── TinyMCE ───────────────────────────────────────────────────────────────
  const editorReady = new Promise((resolve) => {
    if (!window.tinymce) {
      // CDN non raggiungibile: resta la textarea semplice (HTML), il salvataggio funziona comunque
      console.error("TinyMCE non caricato: uso la textarea semplice");
      textarea.classList.add("font-mono");
      resolve(null);
      return;
    }

    window.tinymce.init({
      target: textarea,
      license_key: "gpl",
      language: "it",
      language_url: "https://cdn.jsdelivr.net/npm/tinymce-i18n@26.9.21/langs8/it.js",
      skin: "oxide-dark",
      content_css: "dark",
      height: 640,
      min_height: 400,
      resize: true,
      menubar: "edit insert format table tools",
      branding: false,
      promotion: false,
      browser_spellcheck: true,
      contextmenu: false,
      plugins: "autolink link image media codesample lists advlist table code fullscreen wordcount searchreplace charmap visualblocks help",
      toolbar:
        "undo redo | blocks | bold italic underline strikethrough | bullist numlist | blockquote codesample | " +
        "link image media table hr | removeformat | searchreplace visualblocks code fullscreen",
      toolbar_mode: "sliding",
      toolbar_sticky: true,
      block_formats: "Paragrafo=p; Titolo sezione=h2; Sottotitolo=h3; Titolo minore=h4",
      codesample_languages: [
        { text: "C", value: "c" },
        { text: "C++", value: "cpp" },
        { text: "Python", value: "python" },
        { text: "Java", value: "java" },
        { text: "JavaScript", value: "javascript" },
        { text: "TypeScript", value: "typescript" },
        { text: "PHP", value: "php" },
        { text: "SQL", value: "sql" },
        { text: "Bash / shell", value: "bash" },
        { text: "Makefile", value: "makefile" },
        { text: "HTML / XML", value: "markup" },
        { text: "CSS", value: "css" },
        { text: "JSON", value: "json" },
        { text: "Testo semplice", value: "plaintext" },
      ],
      link_default_target: "_blank",
      link_assume_external_targets: "https",
      link_title: false,
      image_caption: true,
      image_dimensions: false,
      automatic_uploads: true,
      paste_data_images: true,
      images_file_types: "jpg,jpeg,png,gif,webp",
      images_upload_handler: async (blobInfo, progress) => {
        const data = await uploadImage(blobInfo.blob(), progress);
        return data.url;
      },
      // URL delle immagini caricate senza schema/host: funzionano sia in http che in https
      relative_urls: false,
      remove_script_host: true,
      document_base_url: `${BASE_URL}/`,
      media_alt_source: false,
      media_poster: false,
      media_dimensions: false,
      // l'HTML viene comunque ripulito dal server (Core\BlogContent) al salvataggio:
      // qui si evita solo di importare stili e markup inutili quando si incolla
      invalid_elements: "script,style,object,embed,form,input,button,select,textarea,font",
      paste_webkit_styles: "none",
      paste_remove_styles_if_webkit: true,
      content_style: `
        body { font-family: Inter, system-ui, sans-serif; font-size: 15px; line-height: 1.7; max-width: 760px; margin: 1rem auto; padding: 0 1rem; }
        h2 { font-size: 1.35rem; margin: 1.6em 0 .5em; }
        h3 { font-size: 1.1rem; margin: 1.3em 0 .4em; }
        img { max-width: 100%; height: auto; border-radius: 12px; }
        pre { background: #0b1220; border: 1px solid #1e293b; border-radius: 12px; padding: 1rem; font-size: 13px; overflow-x: auto; }
        code { font-size: .9em; }
        blockquote { border-left: 4px solid #60a5fa; margin-left: 0; padding-left: 1rem; color: #cbd5e1; font-style: italic; }
        iframe { width: 100%; aspect-ratio: 16 / 9; height: auto; border-radius: 12px; }
      `,
      setup: (editor) => {
        editor.on("init", () => resolve(editor));
        editor.on("input change undo redo", () => { isDirty = true; });
      },
    }).catch((err) => {
      console.error("TinyMCE init", err);
      resolve(null);
    });
  });

  // ── Contatori caratteri (estratto, SEO) ───────────────────────────────────
  document.querySelectorAll("[data-char-counter]").forEach((counter) => {
    const field = document.getElementById(counter.dataset.charCounter);
    if (!field) return;
    const [min, max] = (counter.dataset.ideal || "0-0").split("-").map(Number);
    const update = () => {
      const len = field.value.trim().length;
      counter.textContent = len ? `${len} caratteri · ideale ${min}–${max}` : `ideale ${min}–${max} caratteri`;
      counter.classList.toggle("text-amber-400", len > 0 && (len < min || len > max));
      counter.classList.toggle("text-emerald-400", len >= min && len <= max);
    };
    field.addEventListener("input", update);
    update();
  });

  form.addEventListener("input", () => { isDirty = true; });

  // ── Aree / argomenti ──────────────────────────────────────────────────────
  function showTaxonomyError(message) {
    if (!taxonomyError) return;
    taxonomyError.textContent = message || "";
    taxonomyError.classList.toggle("hidden", !message);
  }

  async function loadCourses(selectedId = null) {
    if (!courseSelect) return;
    courseSelect.disabled = true;
    try {
      const result = await apiRequest(`${BASE_URL}/api/blog/courses`);
      courseSelect.innerHTML = '<option value="">Nessuna area</option>';
      (Array.isArray(result.data) ? result.data : []).forEach((course) => {
        courseSelect.appendChild(new Option(course.name, course.id, false, String(course.id) === String(selectedId)));
      });
      showTaxonomyError("");
    } catch (e) {
      console.error("loadCourses", e);
      courseSelect.innerHTML = '<option value="">Nessuna area</option>';
      showTaxonomyError(`Impossibile caricare le aree: ${e.message}`);
    } finally {
      courseSelect.disabled = false;
    }
  }

  async function loadSubjects(courseId, selectedId = null) {
    if (!subjectSelect) return;
    subjectSelect.innerHTML = '<option value="">Nessun argomento</option>';
    if (!courseId) return;
    subjectSelect.disabled = true;
    try {
      const result = await apiRequest(`${BASE_URL}/api/blog/subjects?course_id=${encodeURIComponent(courseId)}`);
      (Array.isArray(result.data) ? result.data : []).forEach((sub) => {
        subjectSelect.appendChild(new Option(sub.name, sub.id, false, String(sub.id) === String(selectedId)));
      });
      showTaxonomyError("");
    } catch (e) {
      console.error("loadSubjects", e);
      showTaxonomyError(`Impossibile caricare gli argomenti: ${e.message}`);
    } finally {
      subjectSelect.disabled = false;
    }
  }

  courseSelect?.addEventListener("change", () => loadSubjects(courseSelect.value || null, null));

  document.getElementById("createCourseButton")?.addEventListener("click", async () => {
    const name = (await askText("Nome della nuova area", { title: "Nuova area", placeholder: "Es. Informatica, Matematica...", primaryText: "Crea" }))?.trim();
    if (!name) return;
    try {
      const result = await apiRequest(`${BASE_URL}/api/blog/courses`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, csrf_token: CSRF_TOKEN }),
      });
      // l'API restituisce le aree ordinate per nome: cerco quella appena creata
      const created = (result.data?.courses || []).filter((c) => c.name === name).at(-1);
      await loadCourses(created?.id ?? null);
      await loadSubjects(created?.id ?? null, null);
      isDirty = true;
    } catch (e) {
      showAlert(e.message, "Errore", "error");
    }
  });

  document.getElementById("createSubjectButton")?.addEventListener("click", async () => {
    const courseId = courseSelect?.value;
    if (!courseId) { showAlert("Seleziona prima un'area", "Attenzione"); return; }
    const name = (await askText("Nome del nuovo argomento", { title: "Nuovo argomento", placeholder: "Es. Linguaggio C, Geometria...", primaryText: "Crea" }))?.trim();
    if (!name) return;
    try {
      const result = await apiRequest(`${BASE_URL}/api/blog/subjects`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ course_id: courseId, name, csrf_token: CSRF_TOKEN }),
      });
      const created = (result.data?.subjects || []).filter((s) => s.name === name).at(-1);
      await loadSubjects(courseId, created?.id ?? null);
      isDirty = true;
    } catch (e) {
      showAlert(e.message, "Errore", "error");
    }
  });

  // ── Copertina (featured_image_id) — separata dalle immagini nel contenuto ──
  function setCoverPreview(url) {
    if (!coverPreviewImg) return;
    coverPreviewImg.src = url || "";
    coverPreviewImg.classList.toggle("hidden", !url);
    coverPlaceholder?.classList.toggle("hidden", Boolean(url));
    removeCoverBtn?.classList.toggle("hidden", !url);
  }

  document.getElementById("uploadCoverButton")?.addEventListener("click", () => coverInput?.click());

  coverInput?.addEventListener("change", async () => {
    if (!coverInput.files?.length) return;
    try {
      const data = await uploadImage(coverInput.files[0]);
      if (featuredInput && data?.id) featuredInput.value = String(data.id);
      setCoverPreview(data?.url || null);
      isDirty = true;
    } catch (e) {
      showAlert(e.message, "Errore", "error");
    } finally {
      coverInput.value = "";
    }
  });

  removeCoverBtn?.addEventListener("click", () => {
    if (featuredInput) featuredInput.value = "";
    setCoverPreview(null);
    isDirty = true;
  });

  // ── Salvataggio ───────────────────────────────────────────────────────────
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const editor = await editorReady;
    if (editor) editor.save(); // copia l'HTML dell'editor nella textarea

    const data = Object.fromEntries(new FormData(form).entries());
    data.csrf_token = CSRF_TOKEN;

    if (!data.title?.trim()) {
      showAlert("Inserisci un titolo", "Attenzione");
      document.getElementById("blogTitle")?.focus();
      return;
    }
    const plain    = editor ? editor.getContent({ format: "text" }).trim() : (data.content || "").trim();
    const hasMedia = /<(img|iframe)\b/i.test(data.content || "");
    if (!plain && !hasMedia) {
      showAlert("Il contenuto dell'articolo è vuoto", "Attenzione");
      editor?.focus();
      return;
    }

    const isEdit = Boolean(postId);
    const url    = isEdit ? `${BASE_URL}/api/blog/posts/${postId}` : `${BASE_URL}/api/blog/posts`;

    submitButton?.setAttribute("disabled", "disabled");
    try {
      const result = await apiRequest(url, {
        method: isEdit ? "PUT" : "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });
      isDirty = false;
      await showAlert(result.message || "Articolo salvato", isEdit ? "Aggiornato" : "Salvato", "success");
      window.location.href = `${BASE_URL}/cms/blog`;
    } catch (err) {
      console.error(err);
      showAlert(err.message || "Errore durante il salvataggio", "Errore", "error");
    } finally {
      submitButton?.removeAttribute("disabled");
    }
  });

  // Avviso se si esce dalla pagina con modifiche non salvate
  window.addEventListener("beforeunload", (e) => {
    if (!isDirty) return;
    e.preventDefault();
    e.returnValue = "";
  });

  // ── Init ──────────────────────────────────────────────────────────────────
  (async () => {
    await loadCourses(initialCourseId);
    if (initialCourseId) await loadSubjects(initialCourseId, initialSubjectId);
  })();
});
