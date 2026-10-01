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

        <p class="mt-2-5 mw-prose fs-15 text-muted">
            Pick a section from the sidebar to manage the site.
        </p>
    </div>
</div>
