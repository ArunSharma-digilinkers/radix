<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Infrastructure\Gallery;
use App\Models\Media;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InfrastructureGalleryTest extends TestCase
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

    public function test_a_content_editor_can_save_the_capacity_figure(): void
    {
        Livewire::actingAs($this->editor())
            ->test(Gallery::class)
            ->set('capacity', '10,000 batteries / month')
            ->call('saveCapacity')
            ->assertHasNoErrors();

        $this->assertSame('10,000 batteries / month', SiteSetting::get('infrastructure_capacity'));
    }

    public function test_a_photo_upload_requires_alt_text(): void
    {
        Livewire::actingAs($this->editor())
            ->test(Gallery::class)
            ->set('newFile', UploadedFile::fake()->image('floor.jpg'))
            ->set('newFileAlt', '')
            ->call('upload')
            ->assertHasErrors(['newFileAlt']);
    }

    public function test_a_photo_upload_with_alt_text_is_stored_under_the_factory_collection(): void
    {
        Livewire::actingAs($this->editor())
            ->test(Gallery::class)
            ->set('newFile', UploadedFile::fake()->image('floor.jpg'))
            ->set('newFileAlt', 'The main production floor')
            ->call('upload')
            ->assertHasNoErrors();

        $media = SiteSetting::galleryOwner()->media()->where('collection', Media::COLLECTION_FACTORY)->first();
        $this->assertNotNull($media);
        $this->assertSame('The main production floor', $media->altText());
    }

    public function test_a_video_upload_does_not_require_alt_text(): void
    {
        Livewire::actingAs($this->editor())
            ->test(Gallery::class)
            ->set('newFile', UploadedFile::fake()->create('floor.mp4', 500, 'video/mp4'))
            ->call('upload')
            ->assertHasNoErrors();

        $this->assertSame(1, SiteSetting::galleryOwner()->media()->where('collection', Media::COLLECTION_FACTORY)->count());
    }

    public function test_a_gallery_item_can_be_deleted(): void
    {
        $owner = SiteSetting::galleryOwner();
        $media = $owner->media()->create([
            'collection' => Media::COLLECTION_FACTORY,
            'disk' => 'public',
            'path' => 'infrastructure/floor.jpg',
            'filename' => 'floor.jpg',
            'alt' => ['en' => 'Floor'],
            'sort_order' => 0,
        ]);

        Livewire::actingAs($this->editor())->test(Gallery::class)->call('delete', $media->id);

        $this->assertNull(Media::find($media->id));
    }

    public function test_sales_cannot_manage_infrastructure(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.infrastructure.index'))->assertForbidden();
    }
}
