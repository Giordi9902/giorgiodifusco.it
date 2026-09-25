<?php
/**
 * Form articolo condiviso da blog_create.php e blog_edit.php.
 *
 * @var array|null $post           articolo da modificare (null = nuovo articolo)
 * @var array|null $featuredImage  copertina corrente
 */
$post          = $post ?? null;
$featuredImage = $featuredImage ?? null;
$isEdit        = !empty($post['id']);
$coverUrl      = !empty($featuredImage['path']) ? BASE_URL . '/' . $featuredImage['path'] : null;

$inputClass = 'block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm p-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition';
$smallInputClass = 'block w-full border border-slate-700 bg-slate-950/50 rounded-lg shadow-sm px-2.5 py-1.5 text-[13px] text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition';
$cardClass = 'bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl shadow-slate-950/40';
$e = fn($v) => htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
?>

<form id="blogPostForm" novalidate
      <?= $isEdit ? 'data-post-id="' . (int) $post['id'] . '"' : '' ?>
      class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_340px] gap-6 items-start">

    <!-- Colonna principale: titolo + editor -->
    <div class="space-y-4 min-w-0">
        <div class="<?= $cardClass ?> space-y-4">
            <div>
                <label for="blogTitle" class="block text-sm font-medium text-slate-300 mb-1">Titolo *</label>
                <input type="text" id="blogTitle" name="title" required value="<?= $e($post['title'] ?? '') ?>"
                    class="<?= $inputClass ?> text-base font-medium"
                    placeholder="Es. Come preparo una verifica di matematica">
            </div>

            <div>
                <label for="blogContent" class="block text-sm font-medium text-slate-300 mb-1">Contenuto *</label>
                <!-- TinyMCE sostituisce questa textarea; il contenuto viene salvato come HTML -->
                <textarea name="content" id="blogContent" rows="20"
                    class="<?= $inputClass ?>"><?= $e($post['content'] ?? '') ?></textarea>
                <p class="mt-2 text-[11px] text-slate-500 leading-relaxed">
                    Usa il menu <span class="text-slate-400">Paragrafo</span> per titoli e sottotitoli (compaiono nell'indice dell'articolo).
                    Formule KaTeX: scrivile nel testo tra <code class="text-slate-400">$…$</code> o <code class="text-slate-400">$$…$$</code>.
                    Video: incolla il link YouTube/Vimeo con il pulsante <span class="text-slate-400">Media</span>.
                </p>
            </div>
        </div>
    </div>

    <!-- Colonna laterale: pubblicazione e metadati -->
    <aside class="space-y-4 xl:sticky xl:top-6">

        <div class="<?= $cardClass ?> space-y-4">
            <div class="flex items-center justify-between gap-3">
                <label for="status" class="text-sm font-medium text-slate-300">Stato</label>
                <select id="status" name="status"
                    class="border border-slate-700 rounded-lg text-sm px-3 py-1.5 bg-slate-800 text-slate-200 focus:border-purple-500 outline-none transition">
                    <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Bozza</option>
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

            <div class="flex flex-wrap justify-end gap-2 pt-1">
                <?php if ($isEdit && ($post['status'] ?? '') === 'published'): ?>
                    <a href="<?= BASE_URL ?>/blog/<?= $e($post['slug'] ?? '') ?>" target="_blank"
                        class="px-4 py-2 border border-slate-700 rounded-full text-sm font-medium text-slate-300 hover:bg-slate-800 transition">
                        Vedi ↗
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/cms/blog"
                        class="px-4 py-2 border border-slate-700 rounded-full text-sm font-medium text-slate-300 hover:bg-slate-800 transition">
                        Annulla
                    </a>
                <?php endif; ?>
                <button type="submit" id="blogSubmitButton"
                    class="px-5 py-2 rounded-full border border-purple-500 bg-purple-600 text-sm font-semibold text-white hover:bg-purple-500 shadow-lg shadow-purple-900/20 transition disabled:opacity-60 disabled:cursor-wait">
                    <?= $isEdit ? 'Salva modifiche' : 'Salva articolo' ?>
                </button>
            </div>
        </div>

        <div class="<?= $cardClass ?> space-y-4">
            <div>
                <label for="blogCourseSelect" class="block text-sm font-medium text-slate-300 mb-1">Area</label>
                <select name="course_id" id="blogCourseSelect"
                    data-initial-course-id="<?= $e($post['course_id'] ?? '') ?>"
                    class="<?= $inputClass ?> p-2">
                    <option value="">Caricamento…</option>
                </select>
                <button type="button" id="createCourseButton"
                    class="mt-1 text-[11px] text-purple-300 hover:text-purple-200 inline-flex items-center gap-1">
                    <span class="text-sm leading-none font-bold">+</span> Nuova area
                </button>
            </div>
            <div>
                <label for="blogSubjectSelect" class="block text-sm font-medium text-slate-300 mb-1">Argomento</label>
                <select name="subject_id" id="blogSubjectSelect"
                    data-initial-subject-id="<?= $e($post['subject_id'] ?? '') ?>"
                    class="<?= $inputClass ?> p-2">
                    <option value="">Nessun argomento</option>
                </select>
                <button type="button" id="createSubjectButton"
                    class="mt-1 text-[11px] text-purple-300 hover:text-purple-200 inline-flex items-center gap-1">
                    <span class="text-sm leading-none font-bold">+</span> Nuovo argomento
                </button>
            </div>
            <p id="taxonomyError" class="hidden text-[11px] text-red-300"></p>
        </div>

        <div class="<?= $cardClass ?>">
            <label class="block text-sm font-medium text-slate-300 mb-1">Copertina</label>
            <p class="text-[11px] text-slate-500 mb-3">Mostrata nelle card del blog e come anteprima social. Non finisce nel testo.</p>
            <div class="flex items-center gap-3">
                <div class="shrink-0 w-32 h-[4.5rem] rounded-lg overflow-hidden border border-slate-700 bg-slate-950/50 flex items-center justify-center">
                    <img id="coverPreviewImg" <?= $coverUrl ? 'src="' . $e($coverUrl) . '"' : '' ?>
                        alt="" class="w-full h-full object-cover <?= $coverUrl ? '' : 'hidden' ?>">
                    <span id="coverPreviewPlaceholder" class="text-[10px] text-slate-500 px-2 text-center <?= $coverUrl ? 'hidden' : '' ?>">Nessuna copertina</span>
                </div>
                <div class="flex flex-col gap-1.5">
                    <button type="button" id="uploadCoverButton"
                        class="px-3 py-1.5 rounded-lg border border-slate-700 text-xs font-medium text-slate-200 hover:bg-slate-800 transition">
                        Carica copertina
                    </button>
                    <button type="button" id="removeCoverButton"
                        class="px-3 py-1.5 rounded-lg border border-slate-700 text-xs font-medium text-red-300 hover:bg-red-950/40 hover:border-red-800 transition <?= $coverUrl ? '' : 'hidden' ?>">
                        Rimuovi
                    </button>
                </div>
            </div>
            <input type="file" id="coverImageInput" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
            <input type="hidden" name="featured_image_id" id="featuredImageId"
                value="<?= !empty($post['featured_image_id']) ? (int) $post['featured_image_id'] : '' ?>">
        </div>

        <div class="<?= $cardClass ?> space-y-3">
            <div>
                <label for="blogExcerpt" class="block text-sm font-medium text-slate-300 mb-1">Estratto</label>
                <textarea id="blogExcerpt" name="excerpt" rows="3" maxlength="300"
                    class="<?= $smallInputClass ?>"
                    placeholder="Breve descrizione mostrata nelle card del blog (80–160 caratteri)"><?= $e($post['excerpt'] ?? '') ?></textarea>
                <p class="mt-1 text-right text-[10px] text-slate-500" data-char-counter="blogExcerpt" data-ideal="80-160"></p>
            </div>

            <details class="pt-2 border-t border-slate-800" <?= (!empty($post['seo_title']) || !empty($post['seo_description']) || !empty($post['seo_keywords'])) ? 'open' : '' ?>>
                <summary class="cursor-pointer text-xs font-medium text-slate-400 hover:text-slate-200 select-none py-1">
                    SEO &amp; metadati
                </summary>
                <div class="mt-3 space-y-3">
                    <div>
                        <label for="seoTitle" class="block text-xs font-medium text-slate-400 mb-1">Titolo SEO</label>
                        <input type="text" id="seoTitle" name="seo_title" maxlength="255" value="<?= $e($post['seo_title'] ?? '') ?>"
                            class="<?= $smallInputClass ?>" placeholder="Titolo per Google (se diverso dal titolo)">
                        <p class="mt-1 text-right text-[10px] text-slate-500" data-char-counter="seoTitle" data-ideal="30-60"></p>
                    </div>
                    <div>
                        <label for="seoDescription" class="block text-xs font-medium text-slate-400 mb-1">Meta description</label>
                        <textarea id="seoDescription" name="seo_description" rows="3" maxlength="255"
                            class="<?= $smallInputClass ?>" placeholder="Descrizione mostrata nei risultati di Google"><?= $e($post['seo_description'] ?? '') ?></textarea>
                        <p class="mt-1 text-right text-[10px] text-slate-500" data-char-counter="seoDescription" data-ideal="120-160"></p>
                    </div>
                    <div>
                        <label for="seoKeywords" class="block text-xs font-medium text-slate-400 mb-1">Parole chiave</label>
                        <input type="text" id="seoKeywords" name="seo_keywords" maxlength="255" value="<?= $e($post['seo_keywords'] ?? '') ?>"
                            class="<?= $smallInputClass ?>" placeholder="es. ripetizioni matematica, analisi 1">
                    </div>
                </div>
            </details>
        </div>
    </aside>
</form>

<script src="https://cdn.jsdelivr.net/npm/tinymce@8.9.2/tinymce.min.js" referrerpolicy="origin"></script>
<script src="<?= asset('assets/js/admin_blog_editor.js') ?>"></script>
