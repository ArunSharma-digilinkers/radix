@props(['nav' => []])

{{--
    Sticky header. The brief asks for "sticky/simplified navigation" (§5.4).

    A Bootstrap navbar: below `xl` the links collapse into a panel driven by
    Bootstrap's collapse plugin (data-bs-toggle), which keeps aria-expanded and
    aria-controls on the toggler in sync — a nav that only works with a mouse
    would fail the accessibility requirement in §7 on the majority of visits,
    since most traffic is mobile.
--}}
<header class="rx-header sticky-top border-bottom border-hairline">
    <nav class="navbar navbar-expand-xl p-0" aria-label="Primary">
        <div class="rx-header__inner d-flex flex-wrap align-items-center justify-content-between gap-4 mx-auto w-100">
            <a href="{{ route('home') }}" class="navbar-brand m-0 p-0 flex-shrink-0">
                <img
                    src="{{ asset('images/placeholder/logo.png') }}"
                    alt="Radix Power Solutions — Fit it &amp; Forget it"
                    width="160"
                    height="61"
                    class="rx-header__logo w-auto"
                >
            </a>

            <button
                type="button"
                class="navbar-toggler border-0 p-2 me-n2"
                data-bs-toggle="collapse"
                data-bs-target="#primary-nav"
                aria-controls="primary-nav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="primary-nav" class="collapse navbar-collapse flex-grow-0 w-100 w-xl-auto">
                <ul class="navbar-nav align-items-xl-center gap-xl-4 fs-15 fs-xl-13-5">
                    @foreach ($nav as $item)
                        <li class="nav-item">
                            <a
                                href="{{ $item['href'] }}"
                                @class(['nav-link rx-nav-link', 'active' => url()->current() === $item['href']])
                                @if (url()->current() === $item['href']) aria-current="page" @endif
                            >{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</header>
