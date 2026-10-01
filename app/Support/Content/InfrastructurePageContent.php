<?php

namespace App\Support\Content;

/**
 * Infrastructure page content that has no backing table.
 *
 * process() pairs its titles with HomePageContent::processFlow() — the same
 * four stages the homepage teaser already shows as chips — so the two never
 * drift apart (CLAUDE.md §7: single source, not retyped). Descriptions are
 * generic workflow copy, not a claim about Radix's specific capacity, lead
 * times or headcount (those are on the CLAUDE.md §8 blocked list; this is not).
 *
 * The gallery, capacity figure and certifications are real data — see
 * App\Models\SiteSetting::galleryOwner(), SiteSetting::get('infrastructure_capacity')
 * and App\Models\Certification — and render nothing until Radix supplies them,
 * same treatment as every other unverified-pending-confirmation section.
 */
class InfrastructurePageContent
{
    /**
     * @return list<array{title: string, description: string}>
     */
    public static function process(): array
    {
        $descriptions = [
            'Raw material' => 'Plates, separators and casing components are inspected against spec before they enter production.',
            'Assembly' => 'Cells are built and sealed on the production line, under the same process for every unit that leaves it.',
            'Testing' => 'Every batch goes through the QC lab — charge/discharge, load and safety checks — before it is cleared to ship.',
            'Dispatch' => 'Passed units are packed and routed to dealers, distributors and export shipments.',
        ];

        return collect(HomePageContent::processFlow())
            ->map(fn (string $title) => ['title' => $title, 'description' => $descriptions[$title] ?? ''])
            ->all();
    }
}
