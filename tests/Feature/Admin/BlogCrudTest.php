<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Blog\Form as PostForm;
use App\Livewire\Admin\Blog\Index as BlogIndex;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BlogCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    private function editor(): User
    {
        $user = User::factory()->create();
        $user->assignRole('content-editor');

        return $user;
    }

    public function test_a_content_editor_can_create_a_draft_post_end_to_end(): void
    {
        $category = PostCategory::create(['slug' => 'company-updates', 'name' => ['en' => 'Company Updates'], 'is_active' => true, 'sort_order' => 0]);

        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Inside our expanded solar line')
            ->set('excerpt', 'A quick look at what changed.')
            ->set('body', 'Full body copy here.')
            ->set('categoryId', (string) $category->id)
            ->set('newImage', UploadedFile::fake()->image('post.jpg'))
            ->set('imageAlt', 'Solar panels on a rooftop')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.blog.index'));

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame('Inside our expanded solar line', $post->getTranslation('title', 'en'));
        $this->assertSame('inside-our-expanded-solar-line', $post->slug);
        $this->assertNull($post->published_at);
        $this->assertNotNull($post->image);
    }

    public function test_setting_a_future_publish_date_schedules_the_post(): void
    {
        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Scheduled post')
            ->set('publishedAt', now()->addWeek()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::first();
        $this->assertNotNull($post->published_at);
        $this->assertTrue($post->published_at->isFuture());
        $this->assertFalse($post->isPublished());
    }

    /**
     * The CKEditor toolbar is a client-side limit; this is the one that counts.
     * A post body is echoed unescaped on the public side, so anything that gets
     * past here is stored XSS.
     */
    public function test_the_body_html_is_sanitised_on_save(): void
    {
        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Dangerous post')
            ->set('body', '<h2>Real heading</h2><p onclick="steal()">Body copy</p>'
                .'<script>alert(1)</script><iframe src="https://evil.test"></iframe>'
                .'<a href="javascript:alert(1)">bad link</a><a href="https://radixbattery.com">good link</a>')
            ->call('save')
            ->assertHasNoErrors();

        $body = Post::first()->getTranslation('body', 'en');

        // Kept: the markup the toolbar can actually produce.
        $this->assertStringContainsString('<h2>Real heading</h2>', $body);
        $this->assertStringContainsString('Body copy', $body);
        $this->assertStringContainsString('https://radixbattery.com', $body);

        // Dropped: everything else.
        $this->assertStringNotContainsString('<script', $body);
        $this->assertStringNotContainsString('<iframe', $body);
        $this->assertStringNotContainsString('onclick', $body);
        $this->assertStringNotContainsString('javascript:', $body);
    }

    public function test_an_uploaded_body_image_survives_sanitising(): void
    {
        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Post with a diagram')
            ->set('body', '<figure class="image image-style-side">'
                .'<img src="/storage/editor/2026/08/diagram.png" alt="Wiring diagram" width="800" height="600">'
                .'<figcaption>How it wires up</figcaption></figure>'
                .'<p><img src="javascript:alert(1)" alt="bad"></p>')
            ->call('save')
            ->assertHasNoErrors();

        $body = Post::first()->getTranslation('body', 'en');

        // Kept: the upload, its alt text, its dimensions, and the alignment
        // class the editor uses to lay it out.
        $this->assertStringContainsString('src="/storage/editor/2026/08/diagram.png"', $body);
        $this->assertStringContainsString('alt="Wiring diagram"', $body);
        $this->assertStringContainsString('image-style-side', $body);
        $this->assertStringContainsString('<figcaption>How it wires up</figcaption>', $body);

        // Dropped: a src that is not an image URL at all.
        $this->assertStringNotContainsString('javascript:', $body);
    }

    public function test_an_emptied_editor_stores_an_empty_body_not_a_blank_paragraph(): void
    {
        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Empty body post')
            ->set('body', '<p>&nbsp;</p>')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('', Post::first()->getTranslation('body', 'en'));
    }

    public function test_the_post_form_renders_the_editor_and_meta_panel(): void
    {
        $this->actingAs($this->editor())
            ->get(route('admin.blog.create'))
            ->assertOk()
            // The hook resources/js/admin.js looks for to mount CKEditor.
            ->assertSee('data-rich-text', false)
            ->assertSee('Search appearance');
    }

    /**
     * Regression: the form used to fail validation completely silently. The
     * server rejected the save, but neither x-ui.text-field nor the form
     * rendered a message, so clicking Save simply did nothing and the author
     * had no way to know why. Assert the message is actually on the page, not
     * merely in the error bag.
     */
    public function test_a_rejected_save_tells_the_author_why(): void
    {
        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title' => 'required'])
            ->assertSee('The title field is required.')
            ->assertSee('Not saved.');
    }

    public function test_a_duplicate_slug_is_reported_rather_than_swallowed(): void
    {
        Post::factory()->create(['slug' => 'taken-slug']);

        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'A new post')
            ->set('slug', 'taken-slug')
            ->call('save')
            ->assertHasErrors(['slug' => 'unique'])
            ->assertSee('The slug has already been taken.');
    }

    public function test_the_confirmation_says_whether_the_post_is_live(): void
    {
        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Draft post')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertStringContainsString('draft', session('success'));

        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Live post')
            ->set('publishedAt', now()->subHour()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertStringContainsString('live', session('success'));
    }

    /**
     * Regression: the app ran in UTC while every author types Indian local
     * time into a datetime-local input, which carries no timezone. A post
     * "published now" from the admin was stored 5h30m ahead, counted as
     * scheduled, and never appeared on the public site. Publishing now must
     * mean visible now, whatever the server's timezone is.
     */
    public function test_publish_now_makes_the_post_immediately_visible(): void
    {
        Livewire::actingAs($this->editor())
            ->test(PostForm::class)
            ->set('title', 'Live right away')
            ->call('publishNow')
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::first();

        $this->assertTrue($post->isPublished(), 'Post published from the admin was not live.');
        $this->assertSame(1, Post::published()->count());

        $this->get(route('blog.show', $post))->assertOk();
        $this->get(route('blog.index'))->assertSee('Live right away');
    }

    public function test_the_application_timezone_matches_where_the_authors_are(): void
    {
        // The publish field is timezone-naive, so this config value is what
        // gives a typed time its meaning. UTC here silently delays every post.
        $this->assertSame('Asia/Kolkata', config('app.timezone'));
    }

    public function test_sales_cannot_reach_the_blog(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.blog.index'))->assertForbidden();
    }

    public function test_a_post_can_be_soft_deleted_and_restored(): void
    {
        $post = Post::factory()->create();

        Livewire::actingAs($this->editor())->test(BlogIndex::class)->call('delete', $post->id);
        $this->assertSoftDeleted($post);

        Livewire::actingAs($this->editor())->test(BlogIndex::class)->set('showTrashed', true)->call('restore', $post->id);
        $this->assertNotSoftDeleted($post->fresh());
    }
}
