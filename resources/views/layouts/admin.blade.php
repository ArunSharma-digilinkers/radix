<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    {{-- Read by the editor's upload adapter (resources/js/rich-text.js). --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' — Admin — '.config('app.name') : 'Admin — '.config('app.name') }}</title>

    {{ Vite::fonts() }}
    @vite(['resources/scss/app.scss', 'resources/js/admin.js'])
    @livewireStyles
</head>
{{--
    No resources/js/app.js here — that bundle manually bootstraps its own Alpine
    instance for the public site and carries public-only scroll-reveal JS the
    admin doesn't need. Livewire injects its own bundled Alpine (via
    @livewireScripts below) only on pages that render a Livewire component, so
    the two never collide. Every admin route is a full-page Livewire component,
    which is what the drawer and the account menu rely on.
--}}
<body
    class="min-h-screen bg-surface text-ink"
    x-data="{ nav: false }"
    @keydown.escape.window="nav = false"
>
    @php
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        $initials = collect(preg_split('/\s+/', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    @endphp

    <a href="#admin-content" class="rx-skip-link visually-hidden-focusable">
        Skip to content
    </a>

    {{-- Scrim behind the mobile drawer. Never shown from lg up. --}}
    <div
        x-cloak
        x-show="nav"
        x-transition.opacity.duration.200ms
        @click="nav = false"
        class="rx-admin-scrim d-lg-none"
        aria-hidden="true"
    ></div>

    {{--
        One <aside> serves both breakpoints: off-canvas drawer below lg, fixed
        rail from lg up. The static CSS is the closed state, so the panel is
        already correctly hidden on mobile before Alpine boots — Alpine only
        toggles `.is-open`.
    --}}
    <aside class="rx-admin-aside d-flex flex-column bg-radix-dark" :class="{ 'is-open': nav }">
        <div class="d-flex flex-shrink-0 align-items-center justify-content-between gap-2 px-5 border-bottom rx-rule-on-dark rx-admin-aside__bar">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center">
                <img src="{{ asset('images/placeholder/logo.png') }}" alt="Radix Power Solutions" width="120" height="46" class="h-7 w-auto">
            </a>

            <button
                type="button"
                @click="nav = false"
                class="rx-icon-btn rx-icon-btn--on-dark me-n1-5 d-lg-none"
            >
                <x-admin.icon name="close" class="h-5 w-5" />
                <span class="visually-hidden">Close menu</span>
            </button>
        </div>

        <x-admin.nav />

        <div class="d-flex flex-shrink-0 align-items-center gap-3 px-4 py-3-5 border-top rx-rule-on-dark">
            <span class="rx-avatar-sm rx-avatar-sm--dark-2 d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle font-display fs-12 fw-bold text-on-dark" aria-hidden="true">
                {{ $initials }}
            </span>

            <div class="min-w-0">
                <p class="mb-0 text-truncate fs-13 fw-semibold text-on-dark">{{ $user->name }}</p>
                @if ($role)
                    <p class="mb-0 text-truncate font-mono fs-9 text-uppercase tracking-eyebrow text-on-dark-muted">
                        {{ str_replace('-', ' ', $role) }}
                    </p>
                @endif
            </div>
        </div>
    </aside>

    <div class="rx-admin-main d-flex flex-column min-h-screen">
        <header class="rx-admin-topbar sticky-top d-flex flex-shrink-0 align-items-center gap-3 border-bottom border-hairline bg-white px-4 px-sm-6">
            <button
                type="button"
                @click="nav = true"
                class="rx-icon-btn ms-n1-5 d-lg-none"
            >
                <x-admin.icon name="menu" class="h-5 w-5" />
                <span class="visually-hidden">Open menu</span>
            </button>

            @if (! empty($breadcrumbs ?? []))
                <nav aria-label="Breadcrumb" class="min-w-0">
                    <ol class="d-flex align-items-center gap-1-5 mb-0 fs-13 text-meta">
                        @foreach ($breadcrumbs as $crumb)
                            {{-- Ancestors are noise on a phone; the current page is not. --}}
                            <li @class(['align-items-center gap-1-5', 'd-none d-sm-flex' => ! $loop->last, 'd-flex' => $loop->last])>
                                @if (! $loop->last && ($crumb['href'] ?? null))
                                    <a href="{{ $crumb['href'] }}" class="rx-crumb-link">{{ $crumb['label'] }}</a>
                                    <span aria-hidden="true">/</span>
                                @else
                                    <span class="text-truncate fw-medium text-ink" aria-current="page">{{ $crumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <div class="ms-auto d-flex align-items-center gap-1-5">
                <a
                    href="{{ route('home') }}"
                    target="_blank"
                    rel="noopener"
                    class="rx-icon-btn d-none d-sm-inline-flex align-items-center gap-1-5 px-2-5 fs-13 fw-medium"
                >
                    <x-admin.icon name="external" class="h-4 w-4" />
                    View site
                </a>

                <div class="position-relative" x-data="{ account: false }" @click.outside="account = false">
                    <button
                        type="button"
                        @click="account = ! account"
                        :aria-expanded="account.toString()"
                        class="rx-icon-btn d-flex align-items-center gap-2 px-2 fs-13 fw-semibold text-ink"
                    >
                        <span class="rx-avatar-sm d-flex align-items-center justify-content-center rounded-circle bg-radix-dark font-display fs-11 fw-bold text-on-dark" aria-hidden="true">
                            {{ $initials }}
                        </span>
                        <span class="d-none d-sm-block text-truncate rx-account-name">{{ $user->name }}</span>
                        <x-admin.icon name="chevron" class="h-3-5 w-3-5 text-meta" />
                        <span class="visually-hidden">Account menu</span>
                    </button>

                    <div
                        x-cloak
                        x-show="account"
                        x-transition.origin.top.right.duration.150ms
                        class="rx-account-menu position-absolute end-0 mt-1-5 overflow-hidden rounded-card border border-hairline bg-white"
                    >
                        <div class="border-bottom border-hairline px-4 py-3">
                            <p class="mb-0 text-truncate fs-13 fw-semibold text-ink">{{ $user->name }}</p>
                            <p class="mb-0 text-truncate fs-12 text-meta">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="rx-menu-item d-flex align-items-center gap-2 px-4 py-2-5 fs-13 fw-medium d-sm-none">
                            <x-admin.icon name="external" class="h-4 w-4" />
                            View site
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rx-menu-item w-100 border-0 bg-transparent px-4 py-2-5 text-start fs-13 fw-semibold text-radix-red-deep">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main id="admin-content" class="rx-admin-content mx-auto w-100 flex-1 px-4 px-sm-6 py-6 py-lg-8">
            @if (session('success'))
                <x-admin.alert variant="success" class="mb-5">{{ session('success') }}</x-admin.alert>
            @endif

            @if (session('error'))
                <x-admin.alert variant="error" class="mb-5">{{ session('error') }}</x-admin.alert>
            @endif

            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
