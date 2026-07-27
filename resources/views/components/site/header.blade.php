@props(['nav' => []])

{{--
    Sticky header. The brief asks for "sticky/simplified navigation" (§5.4).

    Below `lg` the links collapse into a disclosure panel. It traps focus while
    open, closes on Escape and on outside click, and the toggle reports its state
    through aria-expanded — a nav that only works with a mouse would fail the
    accessibility requirement in §7 on the majority of visits, since most traffic
    is mobile.
--}}
<header
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    class="sticky top-0 z-40 border-b border-hairline bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/85"
>
    <div class="mx-auto flex max-w-radix items-center justify-between gap-4 px-6 py-3 lg:px-14 lg:py-4">
        <a href="{{ route('home') }}" class="shrink-0 hover:no-underline">
            <img
                src="{{ asset('images/placeholder/logo.png') }}"
                alt="Radix Power Solutions — Fit it &amp; Forget it"
                width="160"
                height="61"
                class="h-9 w-auto lg:h-[2.625rem]"
            >
        </a>

        <nav aria-label="Primary" class="hidden lg:block">
            <ul class="flex items-center gap-5 text-[0.84375rem] text-nav">
                @foreach ($nav as $item)
                    <li><a href="{{ $item['href'] }}" class="hover:text-radix-red-deep">{{ $item['label'] }}</a></li>
                @endforeach
            </ul>
        </nav>

        <div class="hidden items-center gap-2.5 lg:flex">
            <x-ui.button variant="secondary" size="md">Contact</x-ui.button>
            <x-ui.button variant="primary" size="md">Enquire Now</x-ui.button>
        </div>

        <button
            type="button"
            x-on:click="open = ! open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="mobile-nav"
            class="-mr-2 inline-flex items-center justify-center rounded-btn p-2 text-ink lg:hidden"
        >
            <span class="sr-only">Menu</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" class="size-6">
                <template x-if="! open">
                    <g><path d="M4 7h16" /><path d="M4 12h16" /><path d="M4 17h16" /></g>
                </template>
                <template x-if="open">
                    <g><path d="M6 6l12 12" /><path d="M18 6L6 18" /></g>
                </template>
            </svg>
        </button>
    </div>

    <div
        id="mobile-nav"
        x-show="open"
        x-cloak
        x-trap.noscroll="open"
        x-on:click.outside="open = false"
        x-transition.opacity
        class="border-t border-hairline bg-white lg:hidden"
    >
        <nav aria-label="Primary" class="px-6 py-4">
            <ul class="flex flex-col">
                @foreach ($nav as $item)
                    <li>
                        <a href="{{ $item['href'] }}" class="block border-b border-hairline py-3 text-[0.9375rem] text-ink">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-5 flex flex-col gap-2.5">
                <x-ui.button variant="primary" size="lg" class="w-full">Enquire Now</x-ui.button>
                <x-ui.button variant="secondary" size="lg" class="w-full">Contact</x-ui.button>
            </div>
        </nav>
    </div>
</header>
