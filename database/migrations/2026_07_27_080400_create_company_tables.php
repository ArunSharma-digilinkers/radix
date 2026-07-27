<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * About and Infrastructure content: leadership, certifications, the milestone
 * timeline and testimonials.
 *
 * Everything here is on the blocked list in CLAUDE.md §8 — the current site's
 * team figures contradict each other, and there are no named testimonials or
 * visible certifications to carry over. These tables are built and left empty
 * until Radix supplies the real thing.
 *
 * Photos attach through the polymorphic `media` table rather than a path column
 * on each row, so the same upload and alt-text handling applies everywhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('role')->nullable();
            $table->json('bio')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->boolean('is_leadership')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('issuer')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        /* The 2001 → 2025 timeline the brief asks for on About. */
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->index();
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->json('quote');
            $table->string('author_name');
            $table->json('author_role')->nullable();
            $table->string('location')->nullable();
            // 'distributor' | 'fleet' | 'retail' | 'export'
            $table->string('type', 32)->default('retail')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('team_members');
    }
};
