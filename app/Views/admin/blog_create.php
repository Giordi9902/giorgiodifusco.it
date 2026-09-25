<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex items-baseline justify-between gap-2">
        <div>
            <h1 class="text-2xl font-semibold text-slate-50">Nuovo articolo</h1>
            <p class="text-sm text-slate-400">Scrivi con Markdown esteso: grassetto, titoli, liste, immagini, video YouTube/Vimeo, KaTeX.</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/blog"
            class="inline-flex items-center justify-center rounded-full border border-slate-700 px-3 py-1.5 text-sm font-medium text-slate-200 hover:bg-slate-800 transition">
            ← Lista articoli
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- Form -->
        <form id="blogPostForm" class="space-y-4 bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl shadow-slate-950/40">

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Titolo *</label>
                <input type="text" name="title" required
                    class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm p-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                    placeholder="Es. Come preparo una verifica di matematica">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Anteprima <span class="text-slate-500 font-normal">(excerpt)</span></label>
                <textarea name="excerpt" rows="2"
                    class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm p-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                    placeholder="Breve descrizione mostrata nelle card del blog (80–160 caratteri ideale)"></textarea>
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
                    class="block w-full border border-slate-700 bg-slate-950/60 rounded-lg shadow-sm p-3 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition font-mono leading-relaxed resize-y"
                    placeholder="Scrivi il contenuto in Markdown. Usa ## per i titoli, **grassetto**, *corsivo*, \`codice\`, @[video](url) per video..."></textarea>

                <p class="mt-1 text-[10px] text-slate-500">
                    Formule: <code class="text-slate-400">$a^2+b^2=c^2$</code> o <code class="text-slate-400">$$\int_0^1 x\,dx$$</code> · Video: <code class="text-slate-400">@[video](https://youtube.com/watch?v=...)</code>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Corso</label>
                    <select name="course_id" id="blogCourseSelect"
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
            <details class="pt-2 border-t border-slate-800">
                <summary class="cursor-pointer text-xs font-medium text-slate-400 hover:text-slate-200 select-none py-1">
                    SEO &amp; metadati (opzionale)
                </summary>
                <div class="mt-3 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Titolo SEO</label>
                            <input type="text" name="seo_title"
                                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm px-2.5 py-1.5 text-[13px] text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                                placeholder="Titolo per Google (se diverso dal titolo)">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Parole chiave</label>
                            <input type="text" name="seo_keywords"
                                class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm px-2.5 py-1.5 text-[13px] text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                                placeholder="es. ripetizioni matematica, analisi 1">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">
                            Meta description <span class="text-slate-600">(120–160 car. ideale)</span>
                        </label>
                        <textarea name="seo_description" rows="2"
                            class="block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm px-2.5 py-1.5 text-[13px] text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                            placeholder="Descrizione mostrata nei risultati di Google"></textarea>
                    </div>
                </div>
            </details>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2 border-t border-slate-800">
                <div class="flex items-center gap-2 text-sm">
                    <label for="status" class="font-medium text-slate-300">Stato:</label>
                    <select id="status" name="status"
                        class="border border-slate-700 rounded-lg text-sm px-3 py-1.5 bg-slate-800 text-slate-200 focus:border-indigo-500 outline-none transition">
                        <option value="draft">Bozza</option>
                        <option value="published">Pubblicato</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <a href="<?= BASE_URL ?>/admin/blog"
                        class="px-4 py-2 border border-slate-700 rounded-full text-sm font-medium text-slate-300 hover:bg-slate-800 transition">
                        Annulla
                    </a>
                    <button type="submit"
                        class="px-5 py-2 rounded-full border border-indigo-500 bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-500 shadow-lg shadow-indigo-900/20 transition">
                        Salva articolo
                    </button>
                </div>
            </div>

            <input type="hidden" name="featured_image_id" id="featuredImageId">
            <input type="file" id="blogImageInput" accept="image/*" class="hidden">
        </form>

        <!-- Preview -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl shadow-slate-950/40 lg:sticky lg:top-24">
            <h2 class="text-sm font-semibold text-slate-100 mb-3 flex items-center gap-2">
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/30 text-[11px] font-mono">fx</span>
                Anteprima live
            </h2>
            <div id="blogContentPreview" class="prose prose-invert prose-sm max-w-none text-slate-50 min-h-[200px]">
                <p class="text-xs text-slate-500">Inizia a scrivere per vedere l'anteprima.</p>
            </div>
        </div>
    </div>
</div>

<style>
.editor-toolbar-btn {
    @apply inline-flex items-center justify-center h-7 min-w-[1.75rem] px-1.5 rounded text-xs text-slate-300 hover:bg-slate-700 hover:text-slate-100 transition select-none;
}
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
