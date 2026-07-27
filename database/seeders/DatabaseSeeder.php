<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * This seeder is intentionally empty and must stay that way.
     *
     * Radix enters all real content through the admin panel. We do not seed
     * demo, faker or placeholder records into any environment — a stray seeded
     * row that reaches production is worse than an empty table.
     *
     * The one permitted exception is structural data (roles and permissions),
     * which gets its own idempotent seeder in Phase 3 and is invoked
     * explicitly, never from here. Model factories exist for tests only.
     *
     * See CLAUDE.md §1.
     */
    public function run(): void
    {
        //
    }
}
