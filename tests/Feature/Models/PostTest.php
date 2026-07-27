<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * `published_at` carries three states in one column: null is a draft, a past
     * date is live, a future date is scheduled. A boolean flag alongside it would
     * be a second source of truth to contradict.
     */
    public function test_published_excludes_drafts_and_future_dated_posts(): void
    {
        Post::factory()->create(['slug' => 'live']);
        Post::factory()->draft()->create(['slug' => 'draft']);
        Post::factory()->scheduled()->create(['slug' => 'scheduled']);

        $this->assertSame(['live'], Post::published()->pluck('slug')->all());
        $this->assertSame(3, Post::count());
    }

    public function test_a_scheduled_post_goes_live_once_its_date_passes(): void
    {
        $post = Post::factory()->scheduled()->create();

        $this->assertFalse($post->isPublished());

        $this->travel(8)->days();

        $this->assertTrue($post->fresh()->isPublished());
    }

    public function test_related_posts_share_a_category_and_exclude_the_post_itself(): void
    {
        $category = PostCategory::create(['slug' => 'maintenance', 'name' => ['en' => 'Maintenance Tips']]);
        $other = PostCategory::create(['slug' => 'news', 'name' => ['en' => 'Industry News']]);

        $post = Post::factory()->create(['post_category_id' => $category->id]);
        $sibling = Post::factory()->create(['post_category_id' => $category->id]);
        Post::factory()->create(['post_category_id' => $other->id]);
        Post::factory()->draft()->create(['post_category_id' => $category->id]);

        $related = Post::relatedTo($post)->get();

        $this->assertSame([$sibling->id], $related->pluck('id')->all());
    }

    public function test_the_byline_falls_back_to_the_house_name(): void
    {
        $this->assertSame(
            Post::DEFAULT_AUTHOR,
            Post::factory()->create(['user_id' => null, 'author_name' => null])->authorName()
        );
    }

    public function test_an_explicit_author_name_wins_over_the_linked_user(): void
    {
        $user = User::factory()->create(['name' => 'Priya Nair']);

        $fromUser = Post::factory()->create(['user_id' => $user->id, 'author_name' => null]);
        $overridden = Post::factory()->create(['user_id' => $user->id, 'author_name' => 'Team Radix']);

        $this->assertSame('Priya Nair', $fromUser->authorName());
        $this->assertSame('Team Radix', $overridden->authorName());
    }

    public function test_deleting_a_category_leaves_its_posts_intact(): void
    {
        $category = PostCategory::create(['slug' => 'news', 'name' => ['en' => 'Industry News']]);
        $post = Post::factory()->create(['post_category_id' => $category->id]);

        $category->forceDelete();

        $this->assertNotNull($post->fresh(), 'A post must survive losing its category.');
        $this->assertNull($post->fresh()->post_category_id);
    }
}
