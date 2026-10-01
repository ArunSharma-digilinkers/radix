<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Certifications\Form as CertificationForm;
use App\Livewire\Admin\Certifications\Index as CertificationsIndex;
use App\Models\Certification;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CertificationCrudTest extends TestCase
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

    public function test_a_content_editor_can_create_a_certification_with_a_scan(): void
    {
        Livewire::actingAs($this->editor())
            ->test(CertificationForm::class)
            ->set('name', 'ISO 9001:2015')
            ->set('issuer', 'Bureau of Indian Standards')
            ->set('newImage', UploadedFile::fake()->image('cert.jpg'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.certifications.index'));

        $certification = Certification::first();
        $this->assertNotNull($certification);
        $this->assertSame('ISO 9001:2015', $certification->getTranslation('name', 'en'));
        $this->assertNotNull($certification->certificateImage);
        $this->assertSame('certificate', $certification->certificateImage->collection);
    }

    public function test_a_certification_can_be_created_without_a_scan(): void
    {
        Livewire::actingAs($this->editor())
            ->test(CertificationForm::class)
            ->set('name', 'BIS Certified')
            ->call('save')
            ->assertHasNoErrors();

        $certification = Certification::first();
        $this->assertNotNull($certification);
        $this->assertNull($certification->certificateImage);
    }

    public function test_a_certification_can_be_deactivated(): void
    {
        $certification = Certification::factory()->create(['is_active' => true]);

        Livewire::actingAs($this->editor())->test(CertificationsIndex::class)->call('toggleActive', $certification->id);

        $this->assertFalse($certification->fresh()->is_active);
    }

    public function test_a_certification_can_be_deleted(): void
    {
        $certification = Certification::factory()->create();

        Livewire::actingAs($this->editor())->test(CertificationsIndex::class)->call('delete', $certification->id);

        $this->assertNull(Certification::find($certification->id));
    }

    public function test_sales_cannot_manage_certifications(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $this->actingAs($sales)->get(route('admin.certifications.index'))->assertForbidden();
    }
}
