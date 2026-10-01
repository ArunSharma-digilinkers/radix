@props([
    'columns' => [],
    'blurb' => null,
    'certifications' => null,
])

<footer class="rx-footer bg-radix-dark text-on-dark-muted">
    <div class="rx-container">
        <div class="rx-footer__grid">
            <div>
                <img
                    src="{{ asset('images/placeholder/logo-light.png') }}"
                    alt="Radix Power Solutions — Fit it &amp; Forget it"
                    width="160"
                    height="109"
                    loading="lazy"
                    class="h-12 w-auto"
                >
                @if ($blurb)
                    <p class="mb-0 mt-3-5 mw-md fs-13 lh-relaxed">{{ $blurb }}</p>
                @endif
            </div>

            @foreach ($columns as $column)
                <div>
                    <h2 class="mb-0 font-display fs-13 fw-bold text-white">{{ $column['heading'] }}</h2>
                    <ul class="d-flex flex-column gap-2 mt-3 fs-13">
                        @foreach ($column['links'] as $link)
                            <li><a href="{{ $link['href'] }}" class="rx-footer__link">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="d-flex flex-wrap justify-content-between gap-2-5 mt-7 pt-4 border-top rx-rule-on-dark fs-12">
            <p class="mb-0">&copy; {{ now()->year }} Radix Power Solutions Pvt. Ltd.</p>
            @if ($certifications)
                <p class="mb-0">{{ $certifications }}</p>
            @endif
        </div>
    </div>
</footer>
