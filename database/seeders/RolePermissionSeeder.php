<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Roles and permissions are structure, not content — the one exception to the
 * no-seeders rule (CLAUDE.md §1/§5). Uses firstOrCreate throughout, so it is
 * safe to re-run and never truncates. Invoked explicitly
 * (`php artisan db:seed --class=RolePermissionSeeder`), never from
 * DatabaseSeeder, which stays empty.
 *
 * Every admin route and Livewire action is gated by a permission string, not
 * a role name check (CLAUDE.md §5) — including super-admin's, which is why
 * super-admin is granted every permission explicitly below rather than
 * special-cased with a Gate::before bypass. One authorization path to audit.
 */
class RolePermissionSeeder extends Seeder
{
    /** @var list<string> */
    public const PERMISSIONS = [
        'products.manage',
        'blog.manage',
        'careers.manage',
        'pages.manage',
        'media.manage',
        'enquiries.manage',
        'dealers.manage',
        'export.manage',
        'infrastructure.manage',
        'testimonials.manage',
        'users.manage',
    ];

    public function run(): void
    {
        $permissions = collect(self::PERMISSIONS)
            ->mapWithKeys(fn (string $name) => [$name => Permission::firstOrCreate(['name' => $name])]);

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions($permissions->values());

        $contentEditor = Role::firstOrCreate(['name' => 'content-editor']);
        $contentEditor->syncPermissions($permissions->only([
            'products.manage',
            'blog.manage',
            'careers.manage',
            'pages.manage',
            'media.manage',
            'export.manage',
            'infrastructure.manage',
            'testimonials.manage',
        ])->values());

        $sales = Role::firstOrCreate(['name' => 'sales']);
        $sales->syncPermissions($permissions->only([
            'enquiries.manage',
            'dealers.manage',
        ])->values());
    }
}
