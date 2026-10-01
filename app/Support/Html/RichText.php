<?php

namespace App\Support\Html;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Sanitiser for HTML that came out of the admin's CKEditor fields.
 *
 * The editor's own toolbar limits what an author can *insert*, but that limit
 * lives in the browser: source-editing mode, a paste from Word, or a crafted
 * Livewire request can all put arbitrary markup in the property. So the
 * allowlist below — not the toolbar — is what actually decides what reaches
 * the database, and it is deliberately the same short list the toolbar can
 * produce. Anything else (script, style, iframe, on* handlers, javascript:
 * URLs) is dropped rather than escaped.
 *
 * Sanitising on the way IN, not on the way out, means the stored value is
 * already safe for {!! !!} in a public template and is only paid for once per
 * save instead of once per page view.
 */
class RichText
{
    private static ?HtmlSanitizer $sanitizer = null;

    public static function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        return trim(self::sanitizer()->sanitize($html));
    }

    /**
     * True when the markup carries no readable content — CKEditor hands back
     * `<p>&nbsp;</p>` for a field the author emptied, which is not the same
     * string as '' but means the same thing.
     */
    public static function isBlank(?string $html): bool
    {
        $text = strip_tags((string) $html, '<img><hr><table>');
        $text = str_replace(["\u{A0}", '&nbsp;'], ' ', $text);

        return trim($text) === '';
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('u')
                ->allowElement('s')
                ->allowElement('h2')
                ->allowElement('h3')
                ->allowElement('h4')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('blockquote')
                ->allowElement('hr')
                ->allowElement('code')
                ->allowElement('pre')
                ->allowElement('figure')
                ->allowElement('figcaption')
                ->allowElement('img', ['src', 'alt', 'width', 'height'])
                ->allowElement('table')
                ->allowElement('thead')
                ->allowElement('tbody')
                ->allowElement('tr')
                ->allowElement('th', ['colspan', 'rowspan'])
                ->allowElement('td', ['colspan', 'rowspan'])
                ->allowElement('a', ['href', 'title', 'target'])
                // CKEditor carries image alignment and the table wrapper on a
                // class, so dropping `class` would silently undo every layout
                // choice an author makes. It cannot execute anything; `style`
                // stays banned, which is why image resizing is off in the
                // editor config too.
                ->allowAttribute('class', ['figure', 'img'])
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->allowRelativeLinks()
                // Uploads live on our own public disk, so relative /storage
                // URLs are the normal case; remote http(s) covers a pasted one.
                ->allowMediaSchemes(['https', 'http'])
                ->allowRelativeMedias()
                // A link the editor opened in a new tab keeps its opener
                // otherwise, which is a tabnabbing hole on the public site.
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                // Symfony truncates at 20,000 characters by default. A long-form
                // post passes that easily, and silent truncation of a body field
                // is the worst possible failure mode here.
                ->withMaxInputLength(2_000_000)
        );
    }
}
