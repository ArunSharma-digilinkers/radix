<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public blog. The line these tests defend is publication: a draft or a
 * scheduled post must be unreachable, not merely unlisted — an unlisted post
 * whose URL still renders is a leak, since the slug is guessable from the title.
 */
class BlogPageTest extends TestCase
{
    use RefreshDatabase;

    private function category(string $slug = 'company-updates', string $name = 'Company Updates'): PostCategory
    {
        return PostCategory::create([
            'slug' => $slug,
            'name' => ['en' => $name],
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    public function test_the_index_lists_published_posts_only(): void
    {
        $published = Post::factory()->create(['title' => ['en' => 'A published post']]);
        $draft = Post::factory()->draft()->create(['title' => ['en' => 'A draft post']]);
        $scheduled = Post::factory()->scheduled()->create(['title' => ['en' => 'A scheduled post']]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee($published->getTranslation('title', 'en'))
            ->assertDontSee($draft->getTranslation('title', 'en'))
            ->assertDontSee($scheduled->getTranslation('title', 'en'));
    }

    public function test_the_index_renders_an_empty_state_rather_than_placeholder_posts(): void
    {
        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('No posts yet.');
    }

    public function test_a_category_page_shows_only_that_categorys_posts(): void
    {
        $updates = $this->category();
        $news = $this->category('industry-news', 'Industry News');

        $inCategory = Post::factory()->create(['post_category_id' => $updates->id, 'title' => ['en' => 'Belongs here']]);
        $other = Post::factory()->create(['post_category_id' => $news->id, 'title' => ['en' => 'Belongs elsewhere']]);

        $this->get(route('blog.category', $updates))
            ->assertOk()
            ->assertSee($inCategory->getTranslation('title', 'en'))
            ->assertDontSee($other->getTranslation('title', 'en'));
    }

    public function test_a_post_page_renders_its_sanitised_body_as_html(): void
    {
        $post = Post::factory()->create([
            'title' => ['en' => 'Choosing an inverter battery'],
            'body' => ['en' => '<h2>Capacity</h2><p>Pick by <strong>load</strong>, not price.</p>'],
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('Choosing an inverter battery')
            // Escaped output here would show the reader literal <h2> tags.
            ->assertSee('<h2>Capacity</h2>', false)
            ->assertSee('<strong>load</strong>', false);
    }

    public function test_a_draft_or_scheduled_post_is_not_reachable_by_url(): void
    {
        $this->get(route('blog.show', Post::factory()->draft()->create()))->assertNotFound();
        $this->get(route('blog.show', Post::factory()->scheduled()->create()))->assertNotFound();
    }

    public function test_a_post_page_suggests_related_posts_from_the_same_category(): void
    {
        $category = $this->category();

        $post = Post::factory()->create(['post_category_id' => $category->id]);
        $sibling = Post::factory()->create(['post_category_id' => $category->id, 'title' => ['en' => 'Sibling post']]);
        $unrelated = Post::factory()->create(['title' => ['en' => 'Unrelated post']]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee($sibling->getTranslation('title', 'en'))
            ->assertDontSee($unrelated->getTranslation('title', 'en'));
    }

    /**
     * Regression: image URLs were built from APP_URL, so a site served on
     * 127.0.0.1:8000 pointed every <img> at http://localhost/storage/... —
     * port 80, a different document root — and no image ever loaded. They must
     * resolve against whatever host is serving the page.
     */
    public function test_post_images_are_addressed_relative_to_the_serving_host(): void
    {
        $post = Post::factory()->create(['title' => ['en' => 'Post with a picture']]);

        $post->media()->create([
            'collection' => Media::COLLECTION_MAIN,
            'disk' => 'public',
            'path' => 'posts/1/photo.jpg',
            'filename' => 'photo.jpg',
            'mime_type' => 'image/jpeg',
            'alt' => ['en' => 'A battery on the line'],
            'sort_order' => 0,
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('src="/storage/posts/1/photo.jpg"', false)
            ->assertDontSee('http://localhost/storage', false);
    }

    public function test_meta_falls_back_to_the_posts_own_title_and_excerpt(): void
    {
        $post = Post::factory()->create([
            'title' => ['en' => 'Plain title'],
            'excerpt' => ['en' => 'A short summary of the post.'],
            'meta_title' => ['en' => ''],
            'meta_description' => ['en' => ''],
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('<title>Plain title — '.config('app.name').'</title>', false)
            ->assertSee('content="A short summary of the post."', false);
    }
}
