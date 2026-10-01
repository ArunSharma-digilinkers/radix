{{--
    Sidebar navigation. Rendered once — the same <aside> is the desktop rail
    and the mobile drawer, so this markup is never duplicated and the two can
    never drift apart.

    Every entry is permission-filtered in App\Support\Admin\Nav before it gets
    here (CLAUDE.md §5), so there is no @can in this template.
--}}
@php
    $home = App\Support\Admin\Nav::home();
    $homeIsActive = App\Support\Admin\Nav::isActive($home['active']);

    // Shared geometry for every row, active or not, so the active state is a
    // pure colour change and nothing shifts by a pixel when you navigate.
    $row = 'group relative flex items-center gap-3 rounded-btn px-3 py-2 text-[0.84375rem] font-medium transition-colors duration-150';
@endphp

<nav aria-label="Admin" class="flex-1 overflow-y-auto px-3 py-4">
    <a
        href="{{ $home['href'] }}"
        @class([$row, 'bg-radix-dark-2 text-on-dark' => $homeIsActive, 'text-on-dark-muted hover:bg-radix-dark-2 hover:text-on-dark' => ! $homeIsActive])
        @if ($homeIsActive) aria-current="page" @endif
    >
        @if ($homeIsActive)
            <span class="absolute left-0 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-r bg-radix-red" aria-hidden="true"></span>
        @endif

        <x-admin.icon name="{{ $home['icon'] }}" class="h-[1.125rem] w-[1.125rem]" />
        {{ $home['label'] }}
    </a>

    @foreach (App\Support\Admin\Nav::visibleGroups() as $group)
        <p class="mt-6 mb-1.5 px-3 font-mono text-[0.625rem] uppercase tracking-eyebrow text-on-dark-muted">
            {{ $group['label'] }}
        </p>

        <ul class="flex flex-col gap-0.5">
            @foreach ($group['items'] as $item)
                @php $isActive = App\Support\Admin\Nav::isActive($item['active']); @endphp

                <li>
                    @if ($item['href'])
                        <a
                            href="{{ $item['href'] }}"
                            @class([$row, 'bg-radix-dark-2 text-on-dark' => $isActive, 'text-on-dark-muted hover:bg-radix-dark-2 hover:text-on-dark' => ! $isActive])
                            @if ($isActive) aria-current="page" @endif
                        >
                            @if ($isActive)
                                <span class="absolute left-0 top-1/2 h-5 w-0.5 -translate-y-1/2 rounded-r bg-radix-red" aria-hidden="true"></span>
                            @endif

                            <x-admin.icon name="{{ $item['icon'] }}" class="h-[1.125rem] w-[1.125rem]" />
                            {{ $item['label'] }}
                        </a>
                    @else
                        {{-- No route yet: an inert row, not a link to nowhere. --}}
                        <span @class([$row, 'cursor-default text-on-dark-muted']) aria-disabled="true">
                            <x-admin.icon name="{{ $item['icon'] }}" class="h-[1.125rem] w-[1.125rem]" />
                            {{ $item['label'] }}
                            <span class="ml-auto rounded-full bg-radix-dark-2 px-1.5 py-0.5 font-mono text-[0.5625rem] uppercase tracking-eyebrow text-on-dark-muted">Soon</span>
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endforeach
</nav>
