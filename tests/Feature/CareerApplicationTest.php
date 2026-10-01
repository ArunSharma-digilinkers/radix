<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_a_general_application_is_stored(): void
    {
        $response = $this->post(route('careers.apply'), [
            'name' => 'Asha Verma',
            'email' => 'asha@example.com',
            'phone' => '9876543210',
            'cover_note' => 'I would love to join the team.',
            'resume' => UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf'),
        ]);

        $response->assertRedirect(route('careers.index').'#apply');

        $application = JobApplication::first();
        $this->assertNotNull($application);
        $this->assertSame('Asha Verma', $application->name);
        $this->assertNull($application->job_opening_id);
        $this->assertSame(JobApplication::STATUS_NEW, $application->status);
        $this->assertNotNull($application->resume_path);
        Storage::disk('local')->assertExists($application->resume_path);
    }

    public function test_applying_to_a_specific_open_role_links_the_application_to_it(): void
    {
        $opening = JobOpening::factory()->create();

        $this->post(route('careers.apply'), [
            'name' => 'Ravi Kumar',
            'email' => 'ravi@example.com',
            'job_opening_slug' => $opening->slug,
            'resume' => UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf'),
        ]);

        $application = JobApplication::first();
        $this->assertSame($opening->id, $application->job_opening_id);
    }

    public function test_a_slug_for_a_closed_role_is_rejected(): void
    {
        $closed = JobOpening::factory()->closed()->create();

        $response = $this->post(route('careers.apply'), [
            'name' => 'Late Applicant',
            'email' => 'late@example.com',
            'job_opening_slug' => $closed->slug,
            'resume' => UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('job_opening_slug');
        $this->assertSame(0, JobApplication::count());
    }

    public function test_name_email_and_resume_are_required(): void
    {
        $response = $this->post(route('careers.apply'), []);

        $response->assertSessionHasErrors(['name', 'email', 'resume']);
    }

    public function test_the_resume_is_stored_on_the_private_disk_not_the_public_one(): void
    {
        $this->post(route('careers.apply'), [
            'name' => 'Private Files',
            'email' => 'private@example.com',
            'resume' => UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf'),
        ]);

        $application = JobApplication::first();

        $this->assertSame('local', $application->resume_disk);
        Storage::disk('local')->assertExists($application->resume_path);
    }
}
