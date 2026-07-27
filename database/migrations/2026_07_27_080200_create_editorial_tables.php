<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blog. Brief §4 asks for a card grid filterable by category, with author, date
 * and related-post suggestions.
 *
 * There is no separate `authors` table: posts point at the admin user who wrote
 * them, with a display-name override for the house byline ("Team Radix") that the
 * concept uses on every post. A second identity table would be two places to keep
 * a name correct.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('slug')->unique();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();

            $table->string('author_name')->nullable(); // overrides the user's name
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            // Null means draft. A nullable timestamp covers draft, published and
            // scheduled in one column, where a boolean would need a second.
            $table->timestamp('published_at')->nullable()->index();

            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('reading_minutes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_categories');
    }
};
