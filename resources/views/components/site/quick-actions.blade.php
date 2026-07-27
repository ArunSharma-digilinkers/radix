@props([
    'whatsapp' => null,
    'message' => 'Hello Radix, I would like to enquire about your batteries.',
])

{{--
    Persistent Enquire Now + WhatsApp affordance (brief §5.4, §7).

    WhatsApp click-to-chat is called out as very high converting for Indian B2B,
    so it gets a real wa.me link with a prefilled message rather than a decorative
    bubble. The number comes from settings; when it is missing the button is left
    out entirely instead of rendering a dead link.
--}}
<div class="fixed bottom-5 right-5 z-30 flex flex-col items-end gap-2.5 print:hidden">
    <x-ui.button variant="primary" size="md" class="rounded-full shadow-lg">
        Enquire Now
    </x-ui.button>

    @if ($whatsapp)
        <a
            href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}?text={{ rawurlencode($message) }}"
            target="_blank"
            rel="noopener"
            class="radix-pulse flex size-13 items-center justify-center rounded-full bg-whatsapp text-white shadow-lg hover:no-underline"
        >
            <span class="sr-only">Chat with Radix on WhatsApp</span>
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="size-7">
                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.15h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.25-8.23a8.23 8.23 0 0 1 0 16.47zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.8-.79.97-.14.16-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.51.11-.11.25-.29.37-.43.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.48c-.16 0-.43.06-.65.31-.22.25-.85.83-.85 2.03s.87 2.35.99 2.51c.12.17 1.71 2.61 4.15 3.66.58.25 1.03.4 1.39.51.58.19 1.11.16 1.53.1.47-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.11-.22-.17-.47-.29z" />
            </svg>
        </a>
    @endif
</div>
