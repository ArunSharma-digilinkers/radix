<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Enforces the project's schema rules mechanically.
 *
 * These are stated in CLAUDE.md §1, but a rule that lives only in a document
 * erodes. Each of these fails the build instead.
 */
class SchemaConventionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Enum columns cannot be extended without a destructive ALTER, which this
     * project does not allow. Use a string column plus model constants and a
     * Rule::in() validation rule.
     */
    public function test_no_migration_declares_an_enum_or_set_column(): void
    {
        foreach (File::files(database_path('migrations')) as $file) {
            $contents = File::get($file->getPathname());

            $this->assertStringNotContainsString(
                '->enum(',
                $contents,
                "{$file->getFilename()} declares an enum column. Use a string with model constants instead (CLAUDE.md §1)."
            );

            $this->assertStringNotContainsString(
                '->set(',
                $contents,
                "{$file->getFilename()} declares a set column. Use a string or JSON column instead."
            );
        }
    }

    /**
     * Migrations are additive. Dropping or renaming a column in a migration
     * destroys data that cannot be recovered by rolling forward.
     *
     * `dropIfExists` inside down() is exempt: down() is never run in this
     * project, and a create-table migration is expected to declare its inverse.
     */
    public function test_no_migration_drops_or_renames_a_column(): void
    {
        $forbidden = ['->dropColumn(', '->renameColumn(', '->dropIfExists(\'', 'Schema::rename('];

        foreach (File::files(database_path('migrations')) as $file) {
            $contents = File::get($file->getPathname());
            $up = $this->upMethodOf($contents);

            foreach ($forbidden as $needle) {
                $this->assertStringNotContainsString(
                    $needle,
                    $up,
                    "{$file->getFilename()} performs a destructive change in up(). Migrations must be additive (CLAUDE.md §1)."
                );
            }
        }
    }

    /**
     * Content is entered through the admin panel. A seeder that creates records
     * risks demo data reaching production.
     */
    public function test_the_database_seeder_creates_nothing(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['products', 'posts', 'dealers', 'enquiries', 'users', 'testimonials'] as $table) {
            $this->assertSame(
                0,
                DB::table($table)->count(),
                "Seeding populated `{$table}`. Seeders must not create content."
            );
        }
    }

    private function upMethodOf(string $contents): string
    {
        $start = strpos($contents, 'public function up()');

        if ($start === false) {
            return '';
        }

        $end = strpos($contents, 'public function down()', $start);

        return $end === false
            ? substr($contents, $start)
            : substr($contents, $start, $end - $start);
    }
}
