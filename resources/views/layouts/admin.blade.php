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
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
    @livewireStyles
</head>
{{--
    No resources/js/app.js here — that bundle manually bootstraps its own Alpine
    instance for the public site's header/mobile-menu and carries public-only
    scroll-reveal JS the admin doesn't need. Livewire injects its own bundled
    Alpine (via @livewireScripts below) only on pages that render a Livewire
    component, so the two never collide. Every admin route is a full-page
    Livewire component, which is what the drawer and the account menu rely on.
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

    <a href="#admin-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-60 focus:rounded-btn focus:bg-white focus:px-4 focus:py-2 focus:text-[0.84375rem] focus:font-semibold">
        Skip to content
    </a>

    {{-- Scrim behind the mobile drawer. Never shown from lg up. --}}
    <div
        x-cloak
        x-show="nav"
        x-transition.opacity.duration.200ms
        @click="nav = false"
        class="fixed inset-0 z-40 bg-radix-dark/60 lg:hidden"
        aria-hidden="true"
    ></div>

    {{--
        One <aside> serves both breakpoints: off-canvas drawer below lg, fixed
        rail from lg up. The static classes are the closed state, so the panel is
        already correctly hidden on mobile before Alpine boots — Alpine only adds
        the open state. Tailwind v4 puts the important modifier at the END
        (translate-x-0!); the v3 `!translate-x-0` form compiles to nothing.
    --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-radix-dark transition-transform duration-200 ease-out lg:translate-x-0"
        :class="{ 'translate-x-0!': nav }"
    >
        <div class="flex h-14 shrink-0 items-center justify-between gap-2 border-b border-white/10 px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center hover:no-underline">
                <img src="{{ asset('images/placeholder/logo.png') }}" alt="Radix Power Solutions" width="120" height="46" class="h-7 w-auto">
            </a>

            <button
                type="button"
                @click="nav = false"
                class="-mr-1.5 rounded-btn p-1.5 text-on-dark-muted hover:bg-radix-dark-2 hover:text-on-dark lg:hidden"
            >
                <x-admin.icon name="close" class="h-5 w-5" />
                <span class="sr-only">Close menu</span>
            </button>
        </div>

        <x-admin.nav />

        <div class="flex shrink-0 items-center gap-3 border-t border-white/10 px-4 py-3.5">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-radix-dark-2 font-display text-[0.75rem] font-bold text-on-dark" aria-hidden="true">
                {{ $initials }}
            </span>

            <div class="min-w-0">
                <p class="truncate text-[0.8125rem] font-semibold text-on-dark">{{ $user->name }}</p>
                @if ($role)
                    <p class="truncate font-mono text-[0.5625rem] uppercase tracking-eyebrow text-on-dark-muted">
                        {{ str_replace('-', ' ', $role) }}
                    </p>
                @endif
            </div>
        </div>
    </aside>

    <div class="flex min-h-screen flex-col lg:pl-64">
        <header class="sticky top-0 z-30 flex h-14 shrink-0 items-center gap-3 border-b border-hairline bg-white px-4 sm:px-6">
            <button
                type="button"
                @click="nav = true"
                class="-ml-1.5 rounded-btn p-1.5 text-nav hover:bg-surface hover:text-ink lg:hidden"
            >
                <x-admin.icon name="menu" class="h-5 w-5" />
                <span class="sr-only">Open menu</span>
            </button>

            @if (! empty($breadcrumbs ?? []))
                <nav aria-label="Breadcrumb" class="min-w-0">
                    <ol class="flex items-center gap-1.5 text-[0.8125rem] text-meta">
                        @foreach ($breadcrumbs as $crumb)
                            {{-- Ancestors are noise on a phone; the current page is not. --}}
                            <li @class(['items-center gap-1.5', 'hidden sm:flex' => ! $loop->last, 'flex' => $loop->last])>
                                @if (! $loop->last && ($crumb['href'] ?? null))
                                    <a href="{{ $crumb['href'] }}" class="hover:text-ink">{{ $crumb['label'] }}</a>
                                    <span aria-hidden="true">/</span>
                                @else
                                    <span class="truncate font-medium text-ink" aria-current="page">{{ $crumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <div class="ml-auto flex items-center gap-1.5">
                <a
                    href="{{ route('home') }}"
                    target="_blank"
                    rel="noopener"
                    class="hidden items-center gap-1.5 rounded-btn px-2.5 py-1.5 text-[0.8125rem] font-medium text-nav hover:bg-surface hover:text-ink sm:inline-flex"
                >
                    <x-admin.icon name="external" class="h-4 w-4" />
                    View site
                </a>

                <div class="relative" x-data="{ account: false }" @click.outside="account = false">
                    <button
                        type="button"
                        @click="account = ! account"
                        :aria-expanded="account.toString()"
                        class="flex items-center gap-2 rounded-btn px-2 py-1.5 text-[0.8125rem] font-semibold text-ink hover:bg-surface"
                    >
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-radix-dark font-display text-[0.6875rem] font-bold text-on-dark" aria-hidden="true">
                            {{ $initials }}
                        </span>
                        <span class="hidden max-w-36 truncate sm:block">{{ $user->name }}</span>
                        <x-admin.icon name="chevron" class="h-3.5 w-3.5 text-meta" />
                        <span class="sr-only">Account menu</span>
                    </button>

                    <div
                        x-cloak
                        x-show="account"
                        x-transition.origin.top.right.duration.150ms
                        class="absolute right-0 z-40 mt-1.5 w-60 overflow-hidden rounded-card border border-hairline bg-white shadow-[0_18px_40px_rgba(15,27,45,0.12)]"
                    >
                        <div class="border-b border-hairline px-4 py-3">
                            <p class="truncate text-[0.8125rem] font-semibold text-ink">{{ $user->name }}</p>
                            <p class="truncate text-[0.75rem] text-meta">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-2 px-4 py-2.5 text-[0.8125rem] font-medium text-nav hover:bg-surface hover:text-ink sm:hidden">
                            <x-admin.icon name="external" class="h-4 w-4" />
                            View site
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 text-left text-[0.8125rem] font-semibold text-radix-red-deep hover:bg-surface">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main id="admin-content" class="mx-auto w-full max-w-admin flex-1 px-4 py-6 sm:px-6 lg:py-8">
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
