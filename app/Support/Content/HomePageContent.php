<?php

namespace App\Support\Content;

/**
 * Homepage content.
 *
 * PHASE 1 SCAFFOLD — see the note on {@see SiteContent}. Every method here becomes
 * a query in Phase 4. The markup consuming it stays the same, which is the whole
 * point of building the homepage against this class rather than inline literals.
 *
 * Copy is taken from the approved design concept. Product lines, blog posts
 * and testimonials are not here at all — HomeController reads real Product,
 * Post and Testimonial rows, so the homepage can never show a line, an
 * article, or a named quote that doesn't actually exist (CLAUDE.md §8).
 */
class HomePageContent
{
    /**
     * Trust figures. These are the numbers the brief states in §1 and asks to be
     * pulled out as headline stats in §6. They must match everywhere they appear,
     * which is why they live in one place.
     *
     * @return list<array{value: string, label: string}>
     */
    public static function stats(): array
    {
        return [
            ['value' => '25+', 'label' => 'Years manufacturing'],
            ['value' => '650+', 'label' => 'Distributors'],
            ['value' => '10L+', 'label' => 'Customers served'],
            ['value' => '5+', 'label' => 'Export countries'],
        ];
    }

    /**
     * Options for the "Find Your Battery" quick-select.
     *
     * Static for now. Phase 5 derives these from real product attributes and makes
     * the form actually resolve to matching products.
     *
     * @return array<string, array{label: string, placeholder: string, options: list<string>}>
     */
    public static function finder(): array
    {
        return [
            'category' => [
                'label' => 'Category',
                'placeholder' => 'Select category',
                'options' => ['Inverter', 'Automotive', 'Solar', 'E-Rickshaw', 'Bike', 'Lithium'],
            ],
            'application' => [
                'label' => 'Application',
                'placeholder' => 'Select application',
                'options' => ['Home / office', 'Shop / commercial', 'Vehicle', 'Fleet', 'Solar setup'],
            ],
            'backup' => [
                'label' => 'Backup needed',
                'placeholder' => 'Select backup',
                'options' => ['2–4 hours', '4–6 hours', '6–8 hours', '8+ hours'],
            ],
        ];
    }

    /**
     * Components of the Solar Power Generating System.
     *
     * The brief singles this out: it is a complete solution and a higher-value
     * sale that is currently invisible on the site (§6), so it gets its own
     * section rather than a line in the product list.
     *
     * Wattages and capacities are indicative and must be confirmed before launch
     * (brief §11.3).
     *
     * @return list<array{number: string, title: string, description: string}>
     */
    public static function solarComponents(): array
    {
        return [
            ['number' => '01', 'title' => 'Solar Panel', 'description' => '330–550 Wp mono PERC modules'],
            ['number' => '02', 'title' => 'Solar Battery', 'description' => 'C10 tubular, 100–200 Ah'],
            ['number' => '03', 'title' => 'Solar Inverter', 'description' => 'PCU 1–10 kVA, MPPT'],
            ['number' => '04', 'title' => 'Charge Controller', 'description' => 'PWM / MPPT, 12–48 V'],
        ];
    }

    /**
     * @return list<array{number: string, title: string, description: string}>
     */
    public static function whyRadix(): array
    {
        return [
            ['number' => '01', 'title' => '25 years of manufacturing', 'description' => 'A quarter-century of in-house production, not rebadged imports.'],
            ['number' => '02', 'title' => '650+ distributor network', 'description' => 'Stocked dealers across India mean service is always nearby.'],
            ['number' => '03', 'title' => 'ISO & BIS certified', 'description' => 'Quality systems and testing that export buyers can verify.'],
            ['number' => '04', 'title' => 'Now with lithium', 'description' => 'A new lithium line for e-rickshaw and inverter applications.'],
        ];
    }

    /**
     * Manufacturing process steps (brief §4, Infrastructure).
     *
     * @return list<string>
     */
    public static function processFlow(): array
    {
        return ['Raw material', 'Assembly', 'Testing', 'Dispatch'];
    }

    /**
     * Export markets. Kept in step with scripts/build-maps.mjs, which highlights
     * the same countries on the world map.
     *
     * @return list<string>
     */
    public static function exportMarkets(): array
    {
        return ['Nigeria', 'UAE', 'Afghanistan', 'Nepal', 'Bhutan', 'Sri Lanka'];
    }
}
