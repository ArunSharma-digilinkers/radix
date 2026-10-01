<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Images dropped into a CKEditor body field.
 *
 * The response shapes here are not ours to choose — CKEditor's SimpleUploadAdapter
 * reads `url` on success and `error.message` on failure, and shows the author a
 * generic "Couldn't upload file" for anything else. Asserting on them keeps a
 * well-meaning refactor to $request->validate() from silently degrading the
 * error an author sees.
 */
class EditorUploadTest extends TestCase
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

    public function test_a_content_editor_can_upload_an_image(): void
    {
        $response = $this->actingAs($this->editor())
            ->post(route('admin.editor.uploads'), [
                'upload' => UploadedFile::fake()->image('diagram.png'),
            ]);

        $response->assertOk()->assertJsonStructure(['url']);

        $this->assertCount(1, Storage::disk('public')->allFiles('editor/'.now()->format('Y/m')));
    }

    public function test_a_non_image_is_rejected_in_the_shape_ckeditor_can_display(): void
    {
        $this->actingAs($this->editor())
            ->post(route('admin.editor.uploads'), [
                'upload' => UploadedFile::fake()->create('invoice.pdf', 40, 'application/pdf'),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['error' => ['message']]);

        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_a_user_without_media_permission_cannot_upload(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)
            ->post(route('admin.editor.uploads'), ['upload' => UploadedFile::fake()->image('x.png')])
            ->assertForbidden();

        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_a_guest_cannot_upload(): void
    {
        $this->post(route('admin.editor.uploads'), ['upload' => UploadedFile::fake()->image('x.png')])
            ->assertRedirect(route('login'));
    }
}
