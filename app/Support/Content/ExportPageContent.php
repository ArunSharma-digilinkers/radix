<?php

namespace App\Support\Content;

/**
 * Export page content that has no backing table.
 *
 * Per-region blurbs are real data — see App\Models\ExportMarket, queried
 * directly by ExportController — so unlike CareerPageContent's sections this
 * class holds only the generic "how it works" process outline, which is a
 * description of a workflow, not a claim about Radix's current capacity,
 * lead times or specific terms (CLAUDE.md §8 blocks those, not this).
 */
class ExportPageContent
{
    /**
     * @return list<array{title: string, description: string}>
     */
    public static function process(): array
    {
        return [
            [
                'title' => 'Enquiry & quotation',
                'description' => 'Tell us the products, volumes and destination — we scope a quote against your specification.',
            ],
            [
                'title' => 'Agreement & production',
                'description' => 'Terms confirmed, the order enters the production line alongside our domestic manufacturing.',
            ],
            [
                'title' => 'Quality control',
                'description' => 'Every batch is tested before it leaves the factory — the same checks that back every unit we sell.',
            ],
            [
                'title' => 'Packaging & documentation',
                'description' => 'Export-grade packaging plus the commercial, customs and compliance paperwork your shipment needs.',
            ],
            [
                'title' => 'Shipping & customs',
                'description' => 'Coordinated freight and customs clearance, with visibility on where your shipment stands.',
            ],
            [
                'title' => 'Delivery & after-sales',
                'description' => 'Delivered to your door, with the same warranty support "Fit it & Forget it" promises at home.',
            ],
        ];
    }
}
