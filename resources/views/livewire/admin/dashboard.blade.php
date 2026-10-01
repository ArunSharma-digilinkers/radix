<div>
    {{--
        Deliberately empty of numbers. Brief §8 blocks publishing figures the
        client has not confirmed, and a dashboard of invented counts is exactly
        the kind of placeholder that quietly becomes real. Widgets land here
        when the models behind them do.
    --}}
    <x-admin.page-header
        title="Dashboard"
        description="Welcome back, {{ auth()->user()->name }}."
    />

    <div class="rounded-card border border-hairline bg-white px-5 py-6">
        <x-ui.eyebrow>Getting started</x-ui.eyebrow>

        <p class="mt-2.5 max-w-prose text-[0.9375rem] text-muted">
            Pick a section from the sidebar to manage the site. Sections marked
            <span class="font-mono text-[0.6875rem] uppercase tracking-eyebrow text-meta">Soon</span>
            are not built yet.
        </p>
    </div>
</div>
