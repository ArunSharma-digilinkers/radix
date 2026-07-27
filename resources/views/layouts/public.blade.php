{{--
    Base layout for the public site.

    Phase 0 keeps this deliberately bare: it proves the token and font pipeline
    end to end. The real header, navigation, footer and the persistent
    Enquire / WhatsApp affordance are built in Phase 1.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    {{-- Emits the self-hosted @font-face CSS plus preloads for the
         above-the-fold weights. Must come before app.css. --}}
    {{ Vite::fonts() }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @yield('content')
</body>
</html>
