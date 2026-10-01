@props([
    'name',
    /** Stroke width — 1.6 reads correctly at the 18–20px sizes the admin uses. */
    'stroke' => '1.6',
])

@php
    // One inline set rather than an icon package: the admin needs a dozen
    // glyphs, they never change per-request, and an <svg> in the markup costs
    // no extra request. All are drawn on the same 24×24 box with round caps so
    // they sit on a shared optical weight next to 13px nav labels.
    $paths = [
        'dashboard' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/>',
        'products' => '<path d="M12 3.2 20.3 7.6v8.8L12 20.8 3.7 16.4V7.6z"/><path d="M3.7 7.6 12 12l8.3-4.4M12 12v8.8"/>',
        'blog' => '<path d="M6 3.5h7.5l5 5V20a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5V4a.5.5 0 0 1 .5-.5z"/><path d="M13.5 3.5v5h5"/><path d="M8.5 13h7M8.5 16.5h4.5"/>',
        'careers' => '<rect x="3.5" y="7.5" width="17" height="12" rx="2"/><path d="M9 7.5V6a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 6v1.5"/><path d="M3.5 12.5h17"/>',
        'pages' => '<rect x="3.5" y="3.5" width="17" height="17" rx="2.5"/><path d="M3.5 9h17M9 9v11.5"/>',
        'media' => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.7"/><path d="m4.5 17.5 5-4.5 4 3.5 3-2.5 3.5 3.5"/>',
        'enquiries' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.8 6.5 7.3 5.4a1.5 1.5 0 0 0 1.8 0l7.3-5.4"/>',
        'dealers' => '<path d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>',
        'export' => '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.5 2.4 3.8 5.4 3.8 8.5s-1.3 6.1-3.8 8.5c-2.5-2.4-3.8-5.4-3.8-8.5S9.5 5.9 12 3.5z"/>',
        'infrastructure' => '<path d="M3.5 20.5h17"/><path d="M5 20.5V9.5l6-4 6 4v11"/><path d="M9.5 20.5v-5h5v5"/><path d="M9.5 12.5h1M13.5 12.5h1M9.5 9.5h1M13.5 9.5h1"/>',
        'certifications' => '<circle cx="12" cy="9.5" r="5.5"/><path d="m8.2 9.5 2.2 2.2 4-4.4"/><path d="M9 14.3 7.5 20.5 12 18l4.5 2.5-1.5-6.2"/>',
        'testimonials' => '<path d="M4.5 6.5h10a1.5 1.5 0 0 1 1.5 1.5v6a1.5 1.5 0 0 1-1.5 1.5H9l-3.5 3v-3H4.5A1.5 1.5 0 0 1 3 14V8a1.5 1.5 0 0 1 1.5-1.5z"/><path d="M19.5 10.5H20a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-.3l-2.2 2v-2"/>',
        'users' => '<circle cx="9" cy="8.5" r="3.2"/><path d="M2.8 20a6.2 6.2 0 0 1 12.4 0"/><path d="M16 5.6a3.2 3.2 0 0 1 0 6.1M17.4 14.6A5.6 5.6 0 0 1 21.2 20"/>',
        'external' => '<path d="M14 4.5h5.5V10"/><path d="m19.5 4.5-7 7"/><path d="M18 14v4.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 4 18.5v-11A1.5 1.5 0 0 1 5.5 6H10"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'check' => '<circle cx="12" cy="12" r="8.5"/><path d="m8.2 12.3 2.6 2.6 5-5.4"/>',
        'alert' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.6v5M12 16.1v.6"/>',
        'chevron' => '<path d="m6.5 9.5 5.5 5 5.5-5"/>',
    ];
@endphp

<svg
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $stroke }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    {{ $attributes->class('flex-shrink-0') }}
>{!! $paths[$name] ?? $paths['pages'] !!}</svg>
