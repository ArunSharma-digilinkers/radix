@props([
    'title' => null,
    'description' => null,
])

{{--
    Base layout for the public site.

    Page <title> and meta description are passed per page — the brief flags
    keyword-stuffed, duplicated metadata as something to fix (§6), so there is no
    sitewide default beyond the app name. Editable meta per page arrives with the
    CMS in Phase 6.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ? $title.' — '.config('app.name') : config('app.name') }}</title>

    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    {{-- Self-hosted @font-face plus preloads for above-the-fold weights.
         Must precede app.css. --}}
    {{ Vite::fonts() }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-btn focus:bg-radix-red focus:px-4 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-white">
        Skip to content
    </a>

    <x-site.header :nav="$nav ?? []" />

    <main id="main">
        {{ $slot }}
    </main>

    <x-site.footer
        :columns="$footerColumns ?? []"
        :blurb="$footerBlurb ?? null"
        :certifications="$certifications ?? null"
    />

    <x-site.quick-actions :whatsapp="$whatsapp ?? null" />
</body>
</html>
