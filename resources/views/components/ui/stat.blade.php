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
<div {{ $attributes->class('border-t border-hairline pt-5 sm:border-t-0 sm:border-l sm:pt-0 sm:pl-6 lg:pl-8') }}>
    <p class="font-display text-[2rem] font-extrabold leading-none tracking-display text-radix-red lg:text-[2.5rem]">
        {{ $value }}
    </p>
    <p class="mt-2 text-[0.8125rem] text-muted">
        {{ $label }}
    </p>
</div>
