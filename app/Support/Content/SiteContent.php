<?php

namespace App\Support\Content;

/**
 * Site chrome content — navigation, footer and contact details.
 *
 * PHASE 1 SCAFFOLD. These arrays are the single seam between the design build and
 * real data. In Phase 4 this class is replaced by queries against the products,
 * pages and site_settings tables; the Blade templates that consume it do not
 * change, because they only ever see arrays of the same shape.
 *
 * Figures here are the ones the brief states (§4). Anything the brief flags as
 * unverified — team size, current specs, leadership — is deliberately absent.
 * See CLAUDE.md §8.
 */
class SiteContent
{
    /**
     * @return list<array{label: string, href: string}>
     */
    public static function nav(): array
    {
        // Hrefs are '#' until Phase 4 introduces the real routes. They are not
        // rendered as dead links anywhere a user can tab into by accident —
        // see the button component.
        return [
            // About, Products, Infrastructure, Export, Dealers, Blog and
            // Contact are real Phase 4 routes now; the rest stay on-page
            // anchors until their own pages land.
            ['label' => 'Products', 'href' => route('products.index')],
            ['label' => 'Solar Systems', 'href' => '#solar'],
            ['label' => 'Infrastructure', 'href' => route('infrastructure.index')],
            ['label' => 'Export', 'href' => route('export.index')],
            ['label' => 'Dealers', 'href' => route('dealers.index')],
            ['label' => 'About', 'href' => route('about')],
            ['label' => 'Blog', 'href' => route('blog.index')],
            ['label' => 'Contact', 'href' => route('contact')],
        ];
    }

    /**
     * @return list<array{heading: string, links: list<array{label: string, href: string}>}>
     */
    public static function footerColumns(): array
    {
        return [
            [
                'heading' => 'Products',
                'links' => [
                    ['label' => 'Inverter', 'href' => route('products.index')],
                    ['label' => 'Automotive', 'href' => route('products.index')],
                    ['label' => 'Solar Systems', 'href' => '#solar'],
                    ['label' => 'Lithium', 'href' => route('products.index')],
                ],
            ],
            [
                'heading' => 'Company',
                'links' => [
                    ['label' => 'About', 'href' => route('about')],
                    ['label' => 'Infrastructure', 'href' => route('infrastructure.index')],
                    ['label' => 'Export', 'href' => route('export.index')],
                    ['label' => 'Careers', 'href' => route('careers.index')],
                ],
            ],
            [
                'heading' => 'Get in touch',
                'links' => [
                    ['label' => 'Contact us', 'href' => route('contact')],
                    ['label' => 'Find a dealer', 'href' => route('dealers.index')],
                    ['label' => 'Become a dealer', 'href' => route('contact').'#enquiry'],
                    ['label' => 'Export enquiry', 'href' => route('export.index').'#enquire'],
                ],
            ],
        ];
    }

    public static function footerBlurb(): string
    {
        return 'Radix Power Solutions Pvt. Ltd. — 25 years of battery manufacturing. '
            .'Inverter, automotive, solar, e-rickshaw and lithium.';
    }

    public static function certifications(): string
    {
        return 'ISO · BIS certified · Made in India';
    }

    /**
     * WhatsApp Business number for click-to-chat.
     *
     * Null until Radix confirms the number, which keeps a dead link off the page.
     * The toll-free number on the current site is not repeated here for the same
     * reason — brief §11.5 asks for confirmation of current contact details.
     */
    public static function whatsapp(): ?string
    {
        return config('radix.whatsapp');
    }

    /**
     * Toll-free number for the Contact page. Same null-until-confirmed
     * treatment as {@see whatsapp()} — see config/radix.php.
     */
    public static function tollFree(): ?string
    {
        return config('radix.toll_free');
    }
}
