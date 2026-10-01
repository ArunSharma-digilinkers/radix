<?php

namespace App\Support\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/**
 * Admin sidebar nav. Each entry names the permission that must be held to
 * see it (CLAUDE.md §5: gate by permission, not role) — the layout renders
 * only what the logged-in user may reach, so a sales user never even sees a
 * Products link, and a group whose every item is gated away disappears with
 * its heading rather than leaving an empty label behind.
 *
 * `href` is null for domains PR2+ hasn't built a route for yet. Those render
 * as inert rows carrying a "Soon" tag instead of an <a href="#"> — a link
 * that goes nowhere reads as broken to a screen reader, and the tag says
 * plainly what the placeholder means.
 *
 * `active` is a route-name pattern (Route::is() syntax) rather than a URL
 * comparison, so a nested page like products/3/edit still lights up Products.
 */
class Nav
{
    /**
     * The dashboard sits above the groups — it is the panel's home, not a
     * member of any domain, and every authenticated staff member sees it.
     *
     * @return array{label: string, href: string, icon: string, active: string}
     */
    public static function home(): array
    {
        return [
            'label' => 'Dashboard',
            'href' => route('admin.dashboard'),
            'icon' => 'dashboard',
            'active' => 'admin.dashboard',
        ];
    }

    /**
     * @return list<array{label: string, items: list<array{label: string, href: string|null, icon: string, permission: string, active: string|null}>}>
     */
    public static function groups(): array
    {
        return [
            [
                'label' => 'Content',
                'items' => [
                    ['label' => 'Products', 'href' => route('admin.products.index'), 'icon' => 'products', 'permission' => 'products.manage', 'active' => 'admin.products.*'],
                    ['label' => 'Blog', 'href' => route('admin.blog.index'), 'icon' => 'blog', 'permission' => 'blog.manage', 'active' => 'admin.blog.*'],
                    ['label' => 'Careers', 'href' => route('admin.careers.index'), 'icon' => 'careers', 'permission' => 'careers.manage', 'active' => 'admin.careers.*'],
                    ['label' => 'Export Markets', 'href' => route('admin.export.index'), 'icon' => 'export', 'permission' => 'export.manage', 'active' => 'admin.export.*'],
                    ['label' => 'Infrastructure', 'href' => route('admin.infrastructure.index'), 'icon' => 'infrastructure', 'permission' => 'infrastructure.manage', 'active' => 'admin.infrastructure.*'],
                    ['label' => 'Certifications', 'href' => route('admin.certifications.index'), 'icon' => 'certifications', 'permission' => 'infrastructure.manage', 'active' => 'admin.certifications.*'],
                    ['label' => 'Testimonials', 'href' => route('admin.testimonials.index'), 'icon' => 'testimonials', 'permission' => 'testimonials.manage', 'active' => 'admin.testimonials.*'],
                ],
            ],
            [
                'label' => 'Leads',
                'items' => [
                    ['label' => 'Enquiries', 'href' => route('admin.enquiries.index'), 'icon' => 'enquiries', 'permission' => 'enquiries.manage', 'active' => 'admin.enquiries.*'],
                    ['label' => 'Dealers', 'href' => route('admin.dealers.index'), 'icon' => 'dealers', 'permission' => 'dealers.manage', 'active' => 'admin.dealers.*'],
                ],
            ],
            [
                'label' => 'Administration',
                'items' => [
                    ['label' => 'Users', 'href' => route('admin.users.index'), 'icon' => 'users', 'permission' => 'users.manage', 'active' => 'admin.users.*'],
                ],
            ],
        ];
    }

    /**
     * The groups the current user may actually see, with gated-away items and
     * then emptied groups removed.
     *
     * @return list<array{label: string, items: list<array<string, mixed>>}>
     */
    public static function visibleGroups(): array
    {
        $visible = [];

        foreach (self::groups() as $group) {
            $items = array_values(array_filter(
                $group['items'],
                fn (array $item): bool => Gate::allows($item['permission'])
            ));

            if ($items !== []) {
                $visible[] = ['label' => $group['label'], 'items' => $items];
            }
        }

        return $visible;
    }

    /**
     * Whether the given nav entry points at the page being rendered.
     */
    public static function isActive(?string $pattern): bool
    {
        return $pattern !== null && Route::is($pattern);
    }

    /**
     * Flat list of every entry, ungrouped.
     *
     * @return list<array<string, mixed>>
     */
    public static function items(): array
    {
        return array_merge(...array_column(self::groups(), 'items'));
    }
}
