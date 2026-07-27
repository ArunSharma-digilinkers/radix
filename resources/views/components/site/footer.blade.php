@props([
    'columns' => [],
    'blurb' => null,
    'certifications' => null,
])

<footer class="bg-radix-dark px-6 pb-8 pt-12 text-on-dark-muted sm:px-10 lg:px-14 lg:pt-12">
    <div class="mx-auto grid max-w-radix gap-9 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
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
                <p class="mt-3.5 max-w-[16.25rem] text-[0.8125rem] leading-relaxed">{{ $blurb }}</p>
            @endif
        </div>

        @foreach ($columns as $column)
            <div>
                <h2 class="font-display text-[0.8125rem] font-bold text-white">{{ $column['heading'] }}</h2>
                <ul class="mt-3 flex flex-col gap-2 text-[0.8125rem]">
                    @foreach ($column['links'] as $link)
                        <li><a href="{{ $link['href'] }}" class="hover:text-white">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <div class="mx-auto mt-7 flex max-w-radix flex-wrap justify-between gap-2.5 border-t border-white/10 pt-4 text-xs">
        <p>&copy; {{ now()->year }} Radix Power Solutions Pvt. Ltd.</p>
        @if ($certifications)
            <p>{{ $certifications }}</p>
        @endif
    </div>
</footer>
