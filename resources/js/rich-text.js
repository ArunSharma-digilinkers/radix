/**
 * CKEditor 5 for the admin's rich-text fields.
 *
 * Loaded on demand — resources/js/admin.js only imports this module when a page
 * actually renders a [data-rich-text] element, so the editor's ~500 kB chunk
 * never reaches the dashboard, an index table, or the public site.
 *
 * Licence note: this is the GPL build of CKEditor 5 ('licenseKey: GPL'), which
 * is the free self-hosted option. CKEditor is dual-licensed — if Radix wants to
 * ship it under a proprietary licence instead, the key here is what changes.
 */
import {
    Autoformat,
    BlockQuote,
    Bold,
    ClassicEditor,
    Code,
    CodeBlock,
    Essentials,
    Heading,
    HorizontalLine,
    Image,
    ImageBlock,
    ImageCaption,
    ImageInline,
    ImageInsert,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Italic,
    Link,
    List,
    ListProperties,
    Paragraph,
    PasteFromOffice,
    RemoveFormat,
    SimpleUploadAdapter,
    SourceEditing,
    Strikethrough,
    Table,
    TableToolbar,
    Underline,
    WordCount,
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';
import '../css/rich-text.css';

/**
 * The toolbar is the same short list the server-side allowlist in
 * App\Support\Html\RichText accepts. Keep the two in step: a button that
 * produces markup the sanitiser strips looks to an author like the save
 * silently ate their work.
 */
const config = {
    licenseKey: 'GPL',
    plugins: [
        Autoformat,
        BlockQuote,
        Bold,
        Code,
        CodeBlock,
        Essentials,
        Heading,
        HorizontalLine,
        Image,
        ImageBlock,
        ImageCaption,
        ImageInline,
        ImageInsert,
        ImageStyle,
        ImageTextAlternative,
        ImageToolbar,
        ImageUpload,
        Italic,
        Link,
        List,
        ListProperties,
        Paragraph,
        PasteFromOffice,
        RemoveFormat,
        SimpleUploadAdapter,
        SourceEditing,
        Strikethrough,
        Table,
        TableToolbar,
        Underline,
        WordCount,
    ],
    toolbar: {
        items: [
            'undo',
            'redo',
            '|',
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'removeFormat',
            '|',
            'link',
            'blockQuote',
            'code',
            'codeBlock',
            '|',
            'bulletedList',
            'numberedList',
            '|',
            'insertImage',
            'insertTable',
            'horizontalLine',
            '|',
            'sourceEditing',
        ],
        shouldNotGroupWhenFull: false,
    },
    heading: {
        // No H1: the post title is the page's only H1, so offering one here
        // would let an author break the document outline from inside the body.
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
        ],
    },
    link: {
        defaultProtocol: 'https://',
        addTargetToExternalLinks: true,
    },
    image: {
        // No resize handles: resizing writes an inline `style="width:…"`, and
        // the server-side allowlist drops style attributes. Sizing is done with
        // the style buttons below, which are class-based and do survive.
        toolbar: [
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'imageStyle:inline',
            'imageStyle:block',
            'imageStyle:side',
        ],
        insert: { type: 'auto' },
    },
    list: {
        properties: { styles: false, startIndex: false, reversed: false },
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
    },
};

/**
 * Per-field config.
 *
 * Uploading is a capability, not a given: a user without media.manage gets no
 * data-upload-url from the Blade component, and then the image button and the
 * upload adapter are both left out rather than offered and rejected. Existing
 * images in an old post still render either way — only inserting is gated.
 */
function configFor(host) {
    const uploadUrl = host.dataset.uploadUrl;

    if (!uploadUrl) {
        return {
            ...config,
            plugins: config.plugins.filter((plugin) => plugin !== SimpleUploadAdapter),
            toolbar: {
                ...config.toolbar,
                items: config.toolbar.items.filter((item) => item !== 'insertImage'),
            },
        };
    }

    return {
        ...config,
        simpleUpload: {
            uploadUrl,
            // Same-origin POST behind the admin's session cookie; the token
            // comes from the meta tag in layouts/admin.blade.php.
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
        },
    };
}

/** Live editors, so a page change can tear them down instead of leaking them. */
const editors = new Map();

function debounce(fn, wait) {
    let timer;

    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), wait);
    };
}

export async function mount(host) {
    // Second line of defence behind admin.js's data-rich-text-ready flag.
    // Registered before the first await, so two concurrent callers cannot both
    // get past it and stack two editors in the same field.
    if (editors.has(host)) {
        return editors.get(host);
    }

    editors.set(host, null);

    const model = host.dataset.model;

    // Captured before create(), because CKEditor replaces the host element and
    // the replacement is not guaranteed to still be inside the same subtree.
    const componentEl = host.closest('[wire\\:id]');
    const countEl = document.querySelector(host.dataset.countTarget ?? '');
    const form = host.closest('form');

    const editor = await ClassicEditor.create(host, configFor(host));

    editors.set(host, editor);

    /**
     * Push the value into Livewire's client-side state with live=false: no
     * network request per keystroke, but the current HTML rides along with the
     * next request the component makes — which is the save.
     */
    const push = () => {
        if (!componentEl || !window.Livewire) {
            return;
        }

        const component = window.Livewire.find(componentEl.getAttribute('wire:id'));

        component?.set(model, editor.getData(), false);
    };

    editor.model.document.on('change:data', debounce(push, 300));

    // The debounce means the last few keystrokes may still be pending when the
    // author hits Save. Both of these flush it before the request goes out.
    editor.ui.focusTracker.on('change:isFocused', (event, name, isFocused) => {
        if (!isFocused) {
            push();
        }
    });

    form?.addEventListener('submit', push, { capture: true });

    if (countEl) {
        countEl.appendChild(editor.plugins.get('WordCount').wordCountContainer);
    }

    return editor;
}

/** Livewire SPA navigation swaps the body out from under the editor. */
document.addEventListener('livewire:navigating', () => {
    editors.forEach((editor) => editor?.destroy());
    editors.clear();
});
