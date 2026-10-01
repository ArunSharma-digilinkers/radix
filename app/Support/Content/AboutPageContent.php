<?php

namespace App\Support\Content;

/**
 * About page content.
 *
 * PHASE 1-STYLE SCAFFOLD — same seam as {@see HomePageContent}. Phase 4 (or
 * Phase 7, once the client inputs land) replaces these methods with real
 * queries against team_members, certifications and milestones; the template
 * does not change.
 *
 * Trust figures, the product line-up and the "why Radix" pitch are not
 * repeated here — they already have one source in {@see HomePageContent} and
 * this page calls that directly, per CLAUDE.md §7 ("pulled from a single
 * settings source, not retyped into templates").
 *
 * milestones() and leadership() return empty on purpose: brief §11 and
 * CLAUDE.md §8 flag headcount, company history detail and leadership names/
 * bios/photos as unverified and explicitly blocked from production. The
 * template hides each section when its data is empty, the same way the
 * WhatsApp button omits itself rather than render a placeholder.
 */
class AboutPageContent
{
    /**
     * @return list<string>
     */
    public static function story(): array
    {
        return [
            'Radix Power Solutions has spent 25+ years manufacturing batteries in India — '
                .'inverter, automotive, solar, e-rickshaw, bike and, more recently, lithium.',
            'What started as a single production line has grown into a nationwide operation '
                .'backed by a 650+ dealer network, serving 10 lakh-plus customers and export '
                .'partners across 5+ countries.',
            'The approach has not changed: build a battery that works and stand behind it. '
                .'"Fit it & Forget it" is a promise, not a slogan.',
        ];
    }

    /**
     * @return list<string>
     */
    public static function certifications(): array
    {
        return ['ISO Certified', 'BIS Certified', 'Made in India'];
    }

    /**
     * 2001 → present milestone timeline (brief §4). Empty until Radix
     * confirms dates and events — CLAUDE.md §8.
     *
     * @return list<array{year: string, event: string}>
     */
    public static function milestones(): array
    {
        return [];
    }

    /**
     * Leadership names, bios and photos — blocked pending client input.
     * CLAUDE.md §8 is explicit that these must not be invented or guessed.
     *
     * @return list<array{name: string, role: string, photo: string}>
     */
    public static function leadership(): array
    {
        return [];
    }
}
