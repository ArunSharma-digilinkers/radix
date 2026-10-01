@props([
    'value',
    'label',
])

{{--
    A headline trust figure. The brief calls these out specifically: the 650+
    distributor network and 10L+ customer base are "strong trust signals currently
    buried in text" (§6) and belong on the homepage as stats with icons.

    Values come from site settings, never typed into a template — the old site
    contradicts itself on team size and we are not repeating that.
--}}
<div {{ $attributes->class('rx-stat') }}>
    <p class="rx-stat__value mb-0 font-display fw-extrabold lh-none tracking-display text-radix-red">
        {{ $value }}
    </p>
    <p class="mb-0 mt-2 fs-13 text-muted">
        {{ $label }}
    </p>
</div>
