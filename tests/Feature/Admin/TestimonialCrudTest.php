<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Testimonials\Form as TestimonialForm;
use App\Livewire\Admin\Testimonials\Index as TestimonialsIndex;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TestimonialCrudTest extends TestCase
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

    public function test_a_content_editor_can_create_a_testimonial_with_a_photo(): void
    {
        Livewire::actingAs($this->editor())
            ->test(TestimonialForm::class)
            ->set('quote', 'Radix batteries move fast off my shelf.')
            ->set('authorName', 'Rakesh Verma')
            ->set('authorRole', 'Distributor')
            ->set('location', 'Lucknow')
            ->set('type', Testimonial::TYPE_DISTRIBUTOR)
            ->set('newImage', UploadedFile::fake()->image('rakesh.jpg'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.testimonials.index'));

        $testimonial = Testimonial::first();
        $this->assertNotNull($testimonial);
        $this->assertSame('Rakesh Verma', $testimonial->author_name);
        $this->assertSame(Testimonial::TYPE_DISTRIBUTOR, $testimonial->type);
        $this->assertNotNull($testimonial->image);
    }

    public function test_a_testimonial_can_be_created_without_a_photo(): void
    {
        Livewire::actingAs($this->editor())
            ->test(TestimonialForm::class)
            ->set('quote', 'Great service.')
            ->set('authorName', 'No Photo Author')
            ->call('save')
            ->assertHasNoErrors();

        $testimonial = Testimonial::first();
        $this->assertNotNull($testimonial);
        $this->assertNull($testimonial->image);
    }

    public function test_quote_and_author_name_are_required(): void
    {
        Livewire::actingAs($this->editor())
            ->test(TestimonialForm::class)
            ->set('quote', '')
            ->set('authorName', '')
            ->call('save')
            ->assertHasErrors(['quote', 'authorName']);
    }

    public function test_a_testimonial_can_be_deactivated(): void
    {
        $testimonial = Testimonial::factory()->create(['is_active' => true]);

        Livewire::actingAs($this->editor())->test(TestimonialsIndex::class)->call('toggleActive', $testimonial->id);

        $this->assertFalse($testimonial->fresh()->is_active);
    }

    public function test_a_testimonial_can_be_soft_deleted_and_restored(): void
    {
        $testimonial = Testimonial::factory()->create();

        Livewire::actingAs($this->editor())->test(TestimonialsIndex::class)->call('delete', $testimonial->id);
        $this->assertSoftDeleted($testimonial);

        Livewire::actingAs($this->editor())->test(TestimonialsIndex::class)->set('showTrashed', true)->call('restore', $testimonial->id);
        $this->assertNotSoftDeleted($testimonial->fresh());
    }

    public function test_sales_cannot_manage_testimonials(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.testimonials.index'))->assertForbidden();
    }
}
