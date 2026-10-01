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

    // Shared geometry for every row, active or not (.rx-nav-row), so the active
    // state is a pure colour change and nothing shifts by a pixel when you navigate.
    $row = 'rx-nav-row position-relative d-flex align-items-center gap-3';
@endphp

<nav aria-label="Admin" class="flex-1 overflow-y-auto px-3 py-4">
    <a
        href="{{ $home['href'] }}"
        @class([$row, 'is-active' => $homeIsActive])
        @if ($homeIsActive) aria-current="page" @endif
    >
        <x-admin.icon name="{{ $home['icon'] }}" class="h-4-5 w-4-5" />
        {{ $home['label'] }}
    </a>

    @foreach (App\Support\Admin\Nav::visibleGroups() as $group)
        <p class="mb-1-5 mt-6 px-3 font-mono fs-10 text-uppercase tracking-eyebrow text-on-dark-muted">
            {{ $group['label'] }}
        </p>

        <ul class="d-flex flex-column gap-0-5">
            @foreach ($group['items'] as $item)
                @php $isActive = App\Support\Admin\Nav::isActive($item['active']); @endphp

                <li>
                    @if ($item['href'])
                        <a
                            href="{{ $item['href'] }}"
                            @class([$row, 'is-active' => $isActive])
                            @if ($isActive) aria-current="page" @endif
                        >
                            <x-admin.icon name="{{ $item['icon'] }}" class="h-4-5 w-4-5" />
                            {{ $item['label'] }}
                        </a>
                    @else
                        {{-- No route yet: an inert row, not a link to nowhere. --}}
                        <span @class([$row, 'is-disabled']) aria-disabled="true">
                            <x-admin.icon name="{{ $item['icon'] }}" class="h-4-5 w-4-5" />
                            {{ $item['label'] }}
                            <span class="ms-auto rounded-pill bg-radix-dark-2 px-1-5 py-0-5 font-mono fs-9 text-uppercase tracking-eyebrow text-on-dark-muted">Soon</span>
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endforeach
</nav>
