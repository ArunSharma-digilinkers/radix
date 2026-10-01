<?php

namespace App\Support\Content;

/**
 * Career page content that has no backing table.
 *
 * Same seam as {@see AboutPageContent}: open positions are real data (the
 * job_openings table, Phase 2), so the page queries that directly. These
 * three sections don't have — and shouldn't get — a table yet, because the
 * content itself doesn't exist:
 *
 * - Culture photos: CLAUDE.md §6 bans stock photography sitewide ("real
 *   factory/product/team photos only"), and §8 blocks factory/team imagery
 *   pending client assets. There is nothing honest to show yet.
 * - Employee testimonials: §8 blocks "named testimonials" pending client
 *   input — a testimonial with an invented name is worse than none.
 * - Benefits: not on the §8 list by name, but the same principle applies —
 *   listing a benefit Radix doesn't actually offer is a claim, not a
 *   placeholder, and mistakes here become the kind of thing "Fit it & Forget
 *   it" is supposed to be the opposite of.
 *
 * All three return empty until Radix supplies real content; the template
 * hides each section rather than render a placeholder, exactly like
 * AboutPageContent::milestones()/leadership() and ContactPageContent::offices().
 */
class CareerPageContent
{
    /**
     * @return list<array{url: string, alt: string}>
     */
    public static function culturePhotos(): array
    {
        return [];
    }

    /**
     * @return list<array{quote: string, name: string, role: string}>
     */
    public static function employeeTestimonials(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    public static function benefits(): array
    {
        return [];
    }
}
