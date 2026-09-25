<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex items-baseline justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Modifica articolo</h1>
            <p class="text-sm text-slate-400">Aggiorna il contenuto con anteprima live. Supporta Markdown, KaTeX, immagini e video embed.</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/blog"
            class="inline-flex items-center justify-center rounded-full border border-slate-700 px-3 py-1.5 text-sm font-medium text-slate-200 hover:bg-slate-800 transition">
            ← Lista articoli
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- Form -->
        <form id="blogPostForm"
              data-post-id="<?= (int)($post['id'] ?? 0) ?>"
              class="space-y-4 bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl shadow-slate-950/40">
            <input type="hidden" name="id" value="<?= (int)($post['id'] ?? 0) ?>">

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Titolo *</label>
                <input type="text" name="title" required
                    value="<?= htmlspecialchars($post['title'] ?? '') ?>"
                    class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm p-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Anteprima <span class="text-slate-500 font-normal">(excerpt)</span></label>
                <textarea name="excerpt" rows="2"
                    class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm p-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Contenuto *</label>

                <!-- Toolbar -->
                <div class="flex flex-wrap items-center gap-1 mb-1.5 p-1.5 rounded-lg border border-slate-700 bg-slate-900">
                    <button type="button" data-editor-action="bold"       title="Grassetto (**testo**)"  class="editor-toolbar-btn font-bold">B</button>
                    <button type="button" data-editor-action="italic"     title="Corsivo (*testo*)"      class="editor-toolbar-btn italic">I</button>
                    <span class="mx-0.5 h-4 w-px bg-slate-700"></span>
                    <button type="button" data-editor-action="h2"         title="Titolo sezione (## )"   class="editor-toolbar-btn text-[11px]">H2</button>
                    <button type="button" data-editor-action="h3"         title="Sottotitolo (### )"     class="editor-toolbar-btn text-[11px]">H3</button>
                    <span class="mx-0.5 h-4 w-px bg-slate-700"></span>
                    <button type="button" data-editor-action="blockquote" title="Citazione (> testo)"    class="editor-toolbar-btn">&ldquo;</button>
                    <button type="button" data-editor-action="hr"         title="Separatore (---)"       class="editor-toolbar-btn">&#8212;</button>
                    <button type="button" data-editor-action="code"       title="Blocco codice (```)"    class="editor-toolbar-btn font-mono text-[11px]">&lt;/&gt;</button>
                    <span class="mx-0.5 h-4 w-px bg-slate-700"></span>
                    <button type="button" id="insertLinkButton"           title="Inserisci link"         class="editor-toolbar-btn">↗</button>
                    <button type="button" id="insertImageButton"          title="Carica immagine"        class="editor-toolbar-btn">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="M21 15l-5-5L5 21"/>
                        </svg>
                    </button>
                    <button type="button" id="insertVideoButton"          title="Incorpora video YouTube/Vimeo" class="editor-toolbar-btn">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                    </button>
                </div>

                <textarea name="content" id="blogContent" rows="16" required
                    class="block w-full border border-slate-700 bg-slate-950/60 rounded-lg shadow-sm p-3 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition font-mono leading-relaxed resize-y"><?= htmlspecialchars($post['content'] ?? '') ?></textarea>

                <p class="mt-1 text-[10px] text-slate-500">
                    Video: <code class="text-slate-400">@[video](https://youtube.com/watch?v=...)</code> · Formule: <code class="text-slate-400">$a^2+b^2$</code>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Corso</label>
                    <select name="course_id" id="blogCourseSelect"
                        data-initial-course-id="<?= htmlspecialchars($post['course_id'] ?? '') ?>"
                        class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm p-2 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                        <option value="">Nessun corso</option>
                    </select>
                    <button type="button" id="createCourseButton"
                        class="mt-1 text-[11px] text-indigo-300 hover:text-indigo-200 inline-flex items-center gap-1">
                        <span class="text-sm leading-none font-bold">+</span> Nuovo corso
                    </button>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Materia</label>
                    <select name="subject_id" id="blogSubjectSelect"
                        data-initial-subject-id="<?= htmlspecialchars($post['subject_id'] ?? '') ?>"
                        class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm p-2 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition">
                        <option value="">Nessuna materia</option>
                    </select>
                    <button type="button" id="createSubjectButton"
                        class="mt-1 text-[11px] text-indigo-300 hover:text-indigo-200 inline-flex items-center gap-1">
                        <span class="text-sm leading-none font-bold">+</span> Nuova materia
                    </button>
                </div>
            </div>

            <!-- SEO -->
            <details class="pt-2 border-t border-slate-800" <?= (!empty($post['seo_title']) || !empty($post['seo_description'])) ? 'open' : '' ?>>
                <summary class="cursor-pointer text-xs font-medium text-slate-400 hover:text-slate-200 select-none py-1">
                    SEO &amp; metadati
                </summary>
                <div class="mt-3 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Titolo SEO</label>
                            <input type="text" name="seo_title"
                                value="<?= htmlspecialchars($post['seo_title'] ?? '') ?>"
                                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm px-2.5 py-1.5 text-[13px] text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                                placeholder="Titolo per Google">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Parole chiave</label>
                            <input type="text" name="seo_keywords"
                                value="<?= htmlspecialchars($post['seo_keywords'] ?? '') ?>"
                                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm px-2.5 py-1.5 text-[13px] text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                                placeholder="es. ripetizioni matematica, SQL">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">
                            Meta description <span class="text-slate-600">(120–160 car.)</span>
                        </label>
                        <textarea name="seo_description" rows="2"
                            class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm px-2.5 py-1.5 text-[13px] text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"><?= htmlspecialchars($post['seo_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </details>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2 border-t border-slate-800">
                <div class="flex items-center gap-2 text-sm">
                    <label for="status" class="font-medium text-slate-300">Stato:</label>
                    <select id="status" name="status"
                        class="border border-slate-700 rounded-lg text-sm px-3 py-1.5 bg-slate-800 text-slate-200 focus:border-indigo-500 outline-none transition">
                        <option value="draft"     <?= ($post['status'] ?? '') === 'draft'     ? 'selected' : '' ?>>Bozza</option>
                        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Pubblicato</option>
                    </select>
                </div>
                <?php if (!empty($post['published_at'])): ?>
                    <p class="text-[11px] text-slate-500">
                        Pubblicato il <time datetime="<?= date('Y-m-d', strtotime($post['published_at'])) ?>"><?= date('d/m/Y', strtotime($post['published_at'])) ?></time>
                        <?php if (!empty($post['updated_at'])): ?>
                            · aggiornato <?= date('d/m/Y', strtotime($post['updated_at'])) ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <div class="flex justify-end gap-3">
                    <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug'] ?? '') ?>" target="_blank"
                        class="px-4 py-2 border border-slate-700 rounded-full text-sm font-medium text-slate-300 hover:bg-slate-800 transition">
                        Vedi →
                    </a>
                    <button type="submit"
                        class="px-5 py-2 rounded-full border border-indigo-500 bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-500 shadow-lg shadow-indigo-900/20 transition">
                        Salva modifiche
                    </button>
                </div>
            </div>

            <input type="hidden" name="featured_image_id" id="featuredImageId"
                   value="<?= isset($post['featured_image_id']) ? (int)$post['featured_image_id'] : '' ?>">
            <input type="file" id="blogImageInput" accept="image/*" class="hidden">
        </form>

        <!-- Preview -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl shadow-slate-950/40 lg:sticky lg:top-24">
            <h2 class="text-sm font-semibold text-slate-100 mb-3 flex items-center gap-2">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/30 text-[11px] font-mono">fx</span>
                Anteprima live
            </h2>
            <div id="blogContentPreview" class="prose prose-invert prose-sm max-w-none text-slate-50 min-h-[200px]">
                <p class="text-xs text-slate-500">L'anteprima si aggiorna mentre scrivi.</p>
            </div>
        </div>
    </div>
</div>

<style>
.editor-toolbar-btn {
    display: inline-flex; align-items: center; justify-content: center;
    height: 1.75rem; min-width: 1.75rem; padding: 0 0.375rem;
    border-radius: 0.25rem; font-size: 0.75rem;
    color: rgb(203 213 225); transition: background 0.15s, color 0.15s;
    user-select: none;
}
.editor-toolbar-btn:hover { background: rgb(51 65 85); color: rgb(241 245 249); }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css">
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<script src="<?= BASE_URL ?>/public/assets/js/admin_blog_editor.js?v=<?= filemtime(__DIR__ . '/../../public/assets/js/admin_blog_editor.js') ?>"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
