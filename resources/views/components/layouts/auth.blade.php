@props(['title' => null])

{{--
    Minimal centered layout for login/password-reset. Not the public marketing
    layout (no header/footer/quick-actions — those are storefront chrome) and
    not the admin shell (the visitor isn't authenticated yet). No Livewire.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ? $title.' — '.config('app.name') : config('app.name') }}</title>

    {{ Vite::fonts() }}
    @vite(['resources/scss/app.scss'])
</head>
<body class="d-flex align-items-center justify-content-center min-h-screen bg-surface px-6 py-12">
    <div class="w-100 mw-sm">
        <img
            src="{{ asset('images/placeholder/logo.png') }}"
            alt="Radix Power Solutions"
            width="160"
            height="61"
            class="d-block mx-auto h-9 w-auto"
        >

        <div class="rx-auth-card mt-8 rounded-frame border border-hairline bg-white p-7">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
