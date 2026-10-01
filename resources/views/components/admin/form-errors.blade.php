{{--
    Summary of everything that stopped a save.

    Field-level messages (x-ui.text-field, x-ui.select-field) are the primary
    feedback, but on a long form the offending field is often scrolled out of
    view — and a submit that appears to do nothing at all reads as a broken
    button, not as a validation failure. This puts the reason at the top where
    the author is already looking after clicking Save.
--}}
@if ($errors->any())
    <x-admin.alert variant="error" {{ $attributes->class('mb-6') }}>
        <span class="fw-semibold">Not saved.</span>
        {{ $errors->count() === 1 ? 'One field needs attention:' : $errors->count().' fields need attention:' }}

        <ul class="mb-0 mt-1-5 ps-4 fw-normal rx-bullets">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </x-admin.alert>
@endif
