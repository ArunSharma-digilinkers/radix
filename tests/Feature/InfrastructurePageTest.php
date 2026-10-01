<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Media;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfrastructurePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_renders(): void
    {
        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertSee('Where the', false);
    }

    public function test_the_process_flow_shows_all_four_stages(): void
    {
        $response = $this->get(route('infrastructure.index'))->assertOk();

        $response->assertSee('Raw material');
        $response->assertSee('Assembly');
        $response->assertSee('Testing');
        $response->assertSee('Dispatch');
    }

    public function test_the_capacity_figure_is_hidden_until_confirmed(): void
    {
        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertDontSee('Production capacity');
    }

    public function test_the_capacity_figure_shows_once_set(): void
    {
        SiteSetting::create(['key' => 'infrastructure_capacity', 'value' => '10,000 batteries / month']);

        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertSee('10,000 batteries / month');
    }

    public function test_the_gallery_shows_an_honest_message_when_empty(): void
    {
        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertSee('Current factory and QC photos are on their way', false);
    }

    public function test_an_uploaded_gallery_photo_renders(): void
    {
        $owner = SiteSetting::galleryOwner();
        $owner->media()->create([
            'collection' => Media::COLLECTION_FACTORY,
            'disk' => 'public',
            'path' => 'infrastructure/floor.jpg',
            'filename' => 'floor.jpg',
            'mime_type' => 'image/jpeg',
            'alt' => ['en' => 'The main production floor'],
            'sort_order' => 0,
        ]);

        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertSee('alt="The main production floor"', false);
    }

    public function test_certifications_show_an_honest_message_when_empty(): void
    {
        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertSee('Certificate scans are on their way', false);
    }

    public function test_an_active_current_certification_is_shown(): void
    {
        Certification::factory()->create(['name' => ['en' => 'ISO 9001:2015']]);

        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertSee('ISO 9001:2015');
    }

    public function test_an_expired_certification_is_not_shown(): void
    {
        Certification::factory()->expired()->create(['name' => ['en' => 'Lapsed Certificate']]);

        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertDontSee('Lapsed Certificate');
    }

    public function test_an_inactive_certification_is_not_shown(): void
    {
        Certification::factory()->create(['name' => ['en' => 'Hidden Certificate'], 'is_active' => false]);

        $this->get(route('infrastructure.index'))
            ->assertOk()
            ->assertDontSee('Hidden Certificate');
    }
}
