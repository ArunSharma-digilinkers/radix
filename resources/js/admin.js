/**
 * Admin panel entry point.
 *
 * Deliberately tiny and deliberately NOT resources/js/app.js: that bundle
 * bootstraps its own Alpine for the public header, and Livewire injects its own
 * Alpine into every admin page. Two Alpine instances on one page is a hard
 * error, so the admin gets its own entry with no Alpine in it at all.
 *
 * The editor is behind a dynamic import, which Vite splits into its own chunk.
 * Pages with no rich-text field — the dashboard, every index table — never
 * download it.
 */
const SELECTOR = '[data-rich-text]';

let richText = null;

async function boot() {
    const fields = document.querySelectorAll(`${SELECTOR}:not([data-rich-text-ready])`);

    if (!fields.length) {
        return;
    }

    /*
     * Claim the fields SYNCHRONOUSLY, before the first await.
     *
     * boot() runs twice on a cold load — Livewire fires livewire:navigated on
     * the initial page too, not only on real navigations. Marking after the
     * dynamic import resolved let both runs pass the :not() filter and mount
     * the same element concurrently, which is two editors stacked in the field:
     * CKEditor's own "source element already used" guard is set at the end of
     * create(), so two in-flight create() calls never see each other.
     */
    fields.forEach((field) => {
        field.dataset.richTextReady = '1';
    });

    richText ??= import('./rich-text.js');

    const { mount } = await richText;

    fields.forEach(mount);
}

document.addEventListener('DOMContentLoaded', boot);
document.addEventListener('livewire:navigated', boot);
