<?php

namespace Tests\Feature\Admin;

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_three_roles_with_the_documented_permission_split(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->assertSame(
            RolePermissionSeeder::PERMISSIONS,
            Role::findByName('super-admin')->permissions->pluck('name')->all()
        );

        $this->assertEqualsCanonicalizing(
            ['products.manage', 'blog.manage', 'careers.manage', 'pages.manage', 'media.manage', 'export.manage', 'infrastructure.manage', 'testimonials.manage'],
            Role::findByName('content-editor')->permissions->pluck('name')->all()
        );

        $this->assertEqualsCanonicalizing(
            ['enquiries.manage', 'dealers.manage'],
            Role::findByName('sales')->permissions->pluck('name')->all()
        );
    }

    public function test_running_it_twice_creates_no_duplicate_rows(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->assertSame(3, Role::count());
        $this->assertSame(count(RolePermissionSeeder::PERMISSIONS), Permission::count());
    }
}
