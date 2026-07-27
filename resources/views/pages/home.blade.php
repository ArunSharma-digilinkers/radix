{{--
    Phase 0 holding page. Exists to verify the token and font pipeline renders.
    Phase 1 replaces this entirely with the approved homepage concept.
--}}
@extends('layouts.public')

@section('title', 'Radix Power Solutions Pvt. Ltd.')

@section('content')
    <main class="min-h-dvh bg-surface px-6 py-20 sm:px-14">
        <div class="mx-auto max-w-radix">
            <p class="font-mono text-[11px] uppercase tracking-eyebrow text-radix-red">
                Website rebuild &middot; Phase 0
            </p>

            <h1 class="mt-4 font-display text-4xl font-extrabold tracking-display text-radix-dark sm:text-5xl">
                The battery brand India <span class="text-radix-red">runs on</span>.
            </h1>

            <p class="mt-5 max-w-md text-base leading-relaxed text-lead">
                Foundation is in place &mdash; design tokens, self-hosted type and the
                base layout. The homepage build starts in Phase 1.
            </p>

            <div class="mt-10 rounded-card border border-hairline bg-white p-7">
                <p class="font-mono text-[10px] uppercase tracking-eyebrow text-meta">
                    Token check
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ([
                        'radix-red' => 'bg-radix-red',
                        'radix-dark' => 'bg-radix-dark',
                        'ink' => 'bg-ink',
                        'muted' => 'bg-muted',
                        'line' => 'bg-line',
                        'hairline' => 'bg-hairline',
                    ] as $name => $class)
                        <span class="flex items-center gap-2 rounded-btn border border-hairline px-3 py-2">
                            <span class="size-4 rounded-sm {{ $class }}"></span>
                            <span class="font-mono text-[11px] text-muted">{{ $name }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection
