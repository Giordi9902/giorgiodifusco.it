<?php

namespace Core;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Contenuto degli articoli del blog.
 *
 * Gli articoli nuovi sono scritti con TinyMCE e salvati come HTML; quelli vecchi
 * sono in Markdown esteso. Qui si normalizzano entrambi:
 *   - sanitize():      pulisce l'HTML dell'editor con una whitelist di tag/attributi
 *   - toEditorHtml():  HTML da caricare in TinyMCE (i vecchi Markdown vengono convertiti)
 *   - render():        HTML pubblico con classi Tailwind, id sui titoli e indice (TOC)
 *   - plainText():     testo semplice per estratti e tempo di lettura
 */
class BlogContent
{
    /** Tag ammessi e relativi attributi. */
    private const ALLOWED = [
        'p' => [], 'br' => [], 'h2' => [], 'h3' => [], 'h4' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'sub' => [], 'sup' => [], 'span' => [],
        'a' => ['href', 'title'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'figure' => [], 'figcaption' => [],
        'ul' => [], 'ol' => ['start'], 'li' => [],
        'blockquote' => [], 'hr' => [],
        'pre' => ['class'], 'code' => ['class'],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [],
        'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
        'iframe' => ['src', 'title', 'allowfullscreen'],
    ];

    /** Tag rimossi insieme al loro contenuto (gli altri tag sconosciuti vengono "srotolati"). */
    private const DROP_WITH_CONTENT = [
        'script', 'style', 'object', 'embed', 'form', 'input', 'button', 'select',
        'textarea', 'svg', 'math', 'template', 'noscript', 'link', 'meta', 'head', 'title',
    ];

    /** Classi Tailwind applicate all'HTML pubblico (stesso aspetto dei vecchi articoli Markdown). */
    private const CLASSES = [
        'p'          => 'text-sm sm:text-base text-gray-700 leading-relaxed text-justify mb-4',
        'h2'         => 'text-lg sm:text-xl font-semibold text-gray-900 mt-8 mb-2 scroll-mt-20',
        'h3'         => 'text-sm sm:text-base font-semibold text-gray-800 mt-6 mb-2 scroll-mt-20',
        'h4'         => 'text-sm font-semibold text-gray-800 mt-5 mb-2',
        'ul'         => 'list-disc list-outside ml-5 my-4 space-y-1.5 text-sm sm:text-base text-gray-700',
        'ol'         => 'list-decimal list-outside ml-5 my-4 space-y-1.5 text-sm sm:text-base text-gray-700',
        'blockquote' => 'my-4 border-l-4 border-blue-400 pl-4 text-gray-600 italic text-sm bg-blue-50/50 rounded-r-md py-1',
        'hr'         => 'my-6 border-gray-200',
        'a'          => 'text-blue-600 underline underline-offset-2 hover:text-blue-700',
        'img'        => 'my-4 rounded-xl border border-gray-200 max-w-full h-auto shadow-sm',
        'figure'     => 'my-6',
        'figcaption' => 'mt-2 text-center text-xs text-gray-500',
        'strong'     => 'font-semibold text-gray-900',
        'b'          => 'font-semibold text-gray-900',
        'em'         => 'italic',
        'i'          => 'italic',
        'pre'        => 'mt-4 mb-4 rounded-xl bg-gray-950 border border-gray-800 p-4 overflow-x-auto text-xs leading-snug text-gray-100 shadow-md',
        'table'      => 'min-w-full text-sm text-gray-700 border-collapse',
        'th'         => 'border border-gray-200 bg-gray-50 px-3 py-2 text-left font-semibold text-gray-900',
        'td'         => 'border border-gray-200 px-3 py-2 align-top',
    ];

    private const INLINE_CODE_CLASS = 'px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200 text-[0.8rem] font-mono text-gray-800';

    /**
     * true se il contenuto è HTML (TinyMCE), false se è il vecchio Markdown.
     */
    public static function isHtml(string $content): bool
    {
        return (bool) preg_match('/^\s*<(p|h[1-6]|ul|ol|div|blockquote|pre|figure|table|img|iframe|hr|br|strong|em|a|span)\b/i', $content);
    }

    /**
     * Pulisce l'HTML prodotto dall'editor: solo tag/attributi in whitelist,
     * URL http(s)/relativi, iframe solo da YouTube e Vimeo.
     */
    public static function sanitize(string $html): string
    {
        [$doc, $root] = self::load($html);
        self::sanitizeChildren($root);
        return trim(self::innerHtml($root));
    }

    /**
     * HTML da caricare nell'editor: i vecchi articoli Markdown vengono convertiti.
     */
    public static function toEditorHtml(string $content): string
    {
        return self::isHtml($content) ? self::sanitize($content) : self::sanitize(self::markdownToHtml($content));
    }

    /**
     * HTML pubblico dell'articolo. Riempie $toc con i titoli h2/h3 (id, text, level).
     */
    public static function render(string $content, array &$toc): string
    {
        $html = self::isHtml($content) ? $content : self::markdownToHtml($content);

        [$doc, $root] = self::load($html);
        self::sanitizeChildren($root);
        self::decorate($doc, $root, $toc);

        return self::innerHtml($root);
    }

    /**
     * Testo semplice (senza tag né sintassi Markdown), per estratti e conteggio parole.
     */
    public static function plainText(string $content): string
    {
        if (self::isHtml($content)) {
            $text = preg_replace('~<(pre|figure|iframe)\b.*?</\1>~is', ' ', $content);
            $text = preg_replace('~<(br|/p|/div|/h[1-6]|/li|/blockquote|/td|/th)\b[^>]*>~i', ' ', $text);
            $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } else {
            $text = $content;
            $text = preg_replace('/```.*?```/s', ' ', $text);                 // code blocks
            $text = preg_replace('/^@\[video]\([^)]+\)$/m', ' ', $text);      // video embeds
            $text = preg_replace('/!\[([^\]]*)]\([^)]+\)/', '$1', $text);     // images
            $text = preg_replace('/\[([^\]]+)]\([^)]+\)/', '$1', $text);      // links
            $text = preg_replace('/`([^`]+)`/', '$1', $text);                 // inline code
            $text = preg_replace('/\*\*(.+?)\*\*/', '$1', $text);             // bold
            $text = preg_replace('/\*(.+?)\*/', '$1', $text);                 // italic
            $text = preg_replace('/^#{1,6}\s+/m', '', $text);                 // headings
            $text = preg_replace('/^>\s?/m', '', $text);                      // blockquotes
            $text = preg_replace('/^[-*]\s+/m', '', $text);                   // bullet lists
            $text = preg_replace('/^\d+\.\s+/m', '', $text);                  // numbered lists
            $text = preg_replace('/^-{3,}$/m', ' ', $text);                   // horizontal rules
            $text = strip_tags($text);
        }

        $text = preg_replace('/\${1,2}/', '', $text);                         // KaTeX $ delimiters
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    public static function wordCount(string $content): int
    {
        $text = self::plainText($content);
        return $text === '' ? 0 : count(preg_split('/\s+/u', $text));
    }

    // ── Markdown legacy → HTML semplice ─────────────────────────────────────

    public static function markdownToHtml(string $markdown): string
    {
        $lines       = preg_split("/\r?\n/", $markdown) ?: [];
        $inCodeBlock = false;
        $inList      = false;
        $listTag     = 'ul';
        $out         = [];

        $closeList = function () use (&$inList, &$listTag, &$out) {
            if ($inList) {
                $out[]  = '</' . $listTag . '>';
                $inList = false;
            }
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // ``` blocchi di codice
            if (preg_match('/^```(.*)$/', $trimmed, $m)) {
                if (!$inCodeBlock) {
                    $closeList();
                    $inCodeBlock = true;
                    $lang        = preg_replace('/[^a-z0-9+#-]/i', '', trim($m[1] ?? ''));
                    $out[]       = '<pre><code' . ($lang !== '' ? ' class="language-' . $lang . '"' : '') . '>';
                } else {
                    $inCodeBlock = false;
                    // la riga precedente termina con "\n": lo tolgo per non lasciare una riga vuota
                    $out[] = rtrim(array_pop($out), "\n") . '</code></pre>';
                }
                continue;
            }

            if ($inCodeBlock) {
                $last  = array_pop($out);
                $out[] = $last . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . "\n";
                continue;
            }

            // @[video](url)
            if (preg_match('/^@\[video\]\(([^)]+)\)$/', $trimmed, $m)) {
                $closeList();
                $embed = self::videoEmbedUrl(trim($m[1]));
                if ($embed !== '') {
                    $out[] = '<iframe src="' . htmlspecialchars($embed, ENT_QUOTES, 'UTF-8') . '" allowfullscreen></iframe>';
                }
                continue;
            }

            if ($trimmed === '---') {
                $closeList();
                $out[] = '<hr>';
                continue;
            }

            if (strncmp($trimmed, '> ', 2) === 0) {
                $closeList();
                $out[] = '<blockquote>' . self::inlineMarkdown(htmlspecialchars(substr($trimmed, 2), ENT_QUOTES, 'UTF-8')) . '</blockquote>';
                continue;
            }

            $isBullet   = preg_match('/^[-*]\s+(.+)$/', $trimmed, $mBullet) === 1;
            $isNumbered = preg_match('/^\d+\.\s+(.+)$/', $trimmed, $mNum) === 1;
            if ($isBullet || $isNumbered) {
                $tag = $isNumbered ? 'ol' : 'ul';
                if (!$inList || $listTag !== $tag) {
                    $closeList();
                    $listTag = $tag;
                    $out[]   = '<' . $tag . '>';
                    $inList  = true;
                }
                $item  = $isNumbered ? $mNum[1] : $mBullet[1];
                $out[] = '<li>' . self::inlineMarkdown(htmlspecialchars($item, ENT_QUOTES, 'UTF-8')) . '</li>';
                continue;
            }

            if ($trimmed === '') {
                $closeList();
                continue;
            }

            $closeList();

            if (preg_match('/^(#{2,3})\s+(.+)$/', $trimmed, $m)) {
                $level = strlen($m[1]);
                $out[] = "<h{$level}>" . self::inlineMarkdown(htmlspecialchars(trim($m[2]), ENT_QUOTES, 'UTF-8')) . "</h{$level}>";
                continue;
            }

            $out[] = '<p>' . self::inlineMarkdown(htmlspecialchars($line, ENT_QUOTES, 'UTF-8')) . '</p>';
        }

        if ($inCodeBlock) {
            $out[] = '</code></pre>';
        }
        $closeList();

        return implode("\n", $out);
    }

    private static function inlineMarkdown(string $esc): string
    {
        $esc = preg_replace('/!\[([^\]]*)]\(([^)]+)\)/', '<img src="$2" alt="$1">', $esc);
        $esc = preg_replace('/\[([^\]]+)]\(([^)]+)\)/', '<a href="$2">$1</a>', $esc);
        $esc = preg_replace('/`([^`]+)`/', '<code>$1</code>', $esc);
        $esc = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $esc);
        $esc = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $esc);
        return $esc;
    }

    /**
     * URL di embed per YouTube/Vimeo, '' se il video non è riconosciuto.
     */
    public static function videoEmbedUrl(string $url): string
    {
        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }
        return '';
    }

    // ── DOM ─────────────────────────────────────────────────────────────────

    /** @return array{0: DOMDocument, 1: DOMElement} */
    private static function load(string $html): array
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="blog-content-root">' . $html . '</div></body></html>',
            LIBXML_NONET
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('blog-content-root');
        if (!$root) {
            // HTML talmente rotto da non avere la radice: ripartiamo dal solo testo
            $doc  = new DOMDocument('1.0', 'UTF-8');
            $root = $doc->createElement('div');
            $root->appendChild($doc->createTextNode(strip_tags($html)));
            $doc->appendChild($root);
        }
        return [$doc, $root];
    }

    private static function innerHtml(DOMElement $root): string
    {
        $html = '';
        foreach ($root->childNodes as $child) {
            $html .= $root->ownerDocument->saveHTML($child);
        }
        return $html;
    }

    private static function sanitizeChildren(DOMNode $node): void
    {
        // copia: la lista viene modificata durante il ciclo
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
                continue;
            }
            if (!$child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            // h1 e h5/h6 non esistono nel layout dell'articolo: li riportiamo a h2/h4
            if ($tag === 'h1' || $tag === 'h5' || $tag === 'h6') {
                $child = self::renameElement($child, $tag === 'h1' ? 'h2' : 'h4');
                $tag   = strtolower($child->tagName);
            }

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);
                continue;
            }

            if (!array_key_exists($tag, self::ALLOWED)) {
                // tag sconosciuto (div, section, font...): tengo il contenuto, non il tag
                self::sanitizeChildren($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            self::sanitizeAttributes($child, $tag);

            if (($tag === 'iframe' && !$child->hasAttribute('src')) || ($tag === 'img' && !$child->hasAttribute('src'))) {
                $node->removeChild($child);
                continue;
            }

            self::sanitizeChildren($child);
        }
    }

    private static function sanitizeAttributes(DOMElement $el, string $tag): void
    {
        $allowed = self::ALLOWED[$tag];

        foreach (iterator_to_array($el->attributes) as $attr) {
            $name  = strtolower($attr->name);
            $value = trim($attr->value);

            if (!in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->name);
                continue;
            }

            if ($name === 'class') {
                // solo la classe del linguaggio per l'evidenziazione del codice
                preg_match('/\blanguage-[a-z0-9+#-]+\b/i', $value, $m);
                $m ? $el->setAttribute('class', strtolower($m[0])) : $el->removeAttribute('class');
            } elseif ($name === 'href') {
                self::isSafeUrl($value, true) ? $el->setAttribute('href', $value) : $el->removeAttribute('href');
            } elseif ($name === 'src' && $tag === 'img') {
                self::isSafeUrl($value, false) ? $el->setAttribute('src', $value) : $el->removeAttribute('src');
            } elseif ($name === 'src' && $tag === 'iframe') {
                $embed = self::videoEmbedUrl($value);
                $embed !== '' ? $el->setAttribute('src', $embed) : $el->removeAttribute('src');
            } elseif (in_array($name, ['width', 'height', 'colspan', 'rowspan', 'start'], true) && !ctype_digit($value)) {
                $el->removeAttribute($attr->name);
            }
        }
    }

    private static function isSafeUrl(string $url, bool $isLink): bool
    {
        if ($url === '') {
            return false;
        }
        if (preg_match('~^(https?:)?//~i', $url) || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return true;
        }
        if ($isLink && preg_match('~^mailto:~i', $url)) {
            return true;
        }
        // path relativo senza schema (es. "public/uploads/x.png")
        return !preg_match('~^[a-z][a-z0-9+.-]*:~i', $url);
    }

    private static function renameElement(DOMElement $el, string $newTag): DOMElement
    {
        $new = $el->ownerDocument->createElement($newTag);
        while ($el->firstChild) {
            $new->appendChild($el->firstChild);
        }
        $el->parentNode->replaceChild($new, $el);
        return $new;
    }

    /**
     * Classi Tailwind, id dei titoli + TOC, lazy loading, link esterni, wrapper per video e tabelle.
     */
    private static function decorate(DOMDocument $doc, DOMElement $root, array &$toc): void
    {
        $usedIds = [];

        // snapshot: durante il ciclo aggiungo wrapper che non vanno ripercorsi
        $elements = iterator_to_array($root->getElementsByTagName('*'));

        foreach ($elements as $el) {
            $tag       = strtolower($el->tagName);
            $origClass = $el->getAttribute('class');

            if (isset(self::CLASSES[$tag])) {
                $el->setAttribute('class', self::CLASSES[$tag]);
            }

            switch ($tag) {
                case 'h2':
                case 'h3':
                    $text   = trim(preg_replace('/\s+/u', ' ', $el->textContent));
                    $baseId = trim(strtolower(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text)), '-') ?: 'sezione';
                    $id     = $baseId;
                    $suffix = 2;
                    while (in_array($id, $usedIds, true)) {
                        $id = $baseId . '-' . $suffix++;
                    }
                    $usedIds[] = $id;
                    $el->setAttribute('id', $id);
                    $toc[] = ['id' => $id, 'text' => $text, 'level' => $tag === 'h3' ? 3 : 2];
                    break;

                case 'code':
                    $inPre = $el->parentNode instanceof DOMElement && strtolower($el->parentNode->tagName) === 'pre';
                    if ($inPre) {
                        preg_match('/\blanguage-[a-z0-9+#-]+/', $origClass, $m);
                        $el->setAttribute('class', trim('font-mono ' . ($m[0] ?? '')));
                    } else {
                        $el->setAttribute('class', self::INLINE_CODE_CLASS);
                    }
                    break;

                case 'pre':
                    // TinyMCE mette language-xx sul <pre>, highlight.js lo cerca sul <code>:
                    // la classe passa al <code> figlio (che viene visitato subito dopo)
                    $code = null;
                    foreach ($el->childNodes as $c) {
                        if ($c instanceof DOMElement && strtolower($c->tagName) === 'code') {
                            $code = $c;
                            break;
                        }
                    }
                    if (!$code) {
                        $code = $doc->createElement('code');
                        while ($el->firstChild) {
                            $code->appendChild($el->firstChild);
                        }
                        $el->appendChild($code);
                        $elements[] = $code;
                    }
                    if (preg_match('/\blanguage-[a-z0-9+#-]+/', $origClass, $m)
                        && !preg_match('/\blanguage-/', $code->getAttribute('class'))) {
                        $code->setAttribute('class', $m[0]);
                    }
                    break;

                case 'a':
                    $href = $el->getAttribute('href');
                    if (preg_match('~^(https?:)?//~i', $href) && !self::isOwnHost($href)) {
                        $el->setAttribute('target', '_blank');
                        $el->setAttribute('rel', 'noopener noreferrer');
                    }
                    break;

                case 'img':
                    $el->setAttribute('loading', 'lazy');
                    $el->setAttribute('decoding', 'async');
                    if (!$el->hasAttribute('alt')) {
                        $el->setAttribute('alt', '');
                    }
                    break;

                case 'iframe':
                    $el->setAttribute('class', 'absolute inset-0 w-full h-full rounded-xl');
                    $el->setAttribute('loading', 'lazy');
                    $el->setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
                    $el->setAttribute('allowfullscreen', '');
                    $el->removeAttribute('width');
                    $el->removeAttribute('height');
                    $wrapper = self::wrap($doc, $el, 'relative my-6 pb-[56.25%] h-0 overflow-hidden rounded-xl border border-gray-200 shadow-md');
                    // TinyMCE mette il video dentro un <p>: un <div> lì dentro non è HTML valido
                    $parent = $wrapper->parentNode;
                    if ($parent instanceof DOMElement && strtolower($parent->tagName) === 'p') {
                        $parent->parentNode->insertBefore($wrapper, $parent->nextSibling);
                        if (trim($parent->textContent) === '' && !$parent->getElementsByTagName('*')->length) {
                            $parent->parentNode->removeChild($parent);
                        }
                    }
                    break;

                case 'table':
                    self::wrap($doc, $el, 'my-6 overflow-x-auto');
                    break;
            }
        }
    }

    private static function wrap(DOMDocument $doc, DOMElement $el, string $class): DOMElement
    {
        $wrapper = $doc->createElement('div');
        $wrapper->setAttribute('class', $class);
        $el->parentNode->replaceChild($wrapper, $el);
        $wrapper->appendChild($el);
        return $wrapper;
    }

    private static function isOwnHost(string $url): bool
    {
        $host = parse_url(str_starts_with($url, '//') ? 'https:' . $url : $url, PHP_URL_HOST);
        $own  = parse_url(BASE_URL, PHP_URL_HOST);
        return $host && $own && preg_replace('/^www\./', '', strtolower($host)) === preg_replace('/^www\./', '', strtolower($own));
    }
}
