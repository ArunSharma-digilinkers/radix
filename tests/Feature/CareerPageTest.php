<?php

namespace Tests\Feature;

use App\Models\JobOpening;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareerPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_renders(): void
    {
        $this->get(route('careers.index'))
            ->assertOk()
            ->assertSee('Build the power behind', false);
    }

    public function test_it_lists_open_positions_only(): void
    {
        $open = JobOpening::factory()->create(['title' => ['en' => 'Production Supervisor']]);
        $draft = JobOpening::factory()->draft()->create(['title' => ['en' => 'Unpublished Role']]);
        $closed = JobOpening::factory()->closed()->create(['title' => ['en' => 'Closed Role']]);

        $response = $this->get(route('careers.index'))->assertOk();

        $response->assertSee('Production Supervisor');
        $response->assertDontSee('Unpublished Role');
        $response->assertDontSee('Closed Role');
    }

    public function test_it_shows_the_send_your_cv_fallback_when_nothing_is_open(): void
    {
        $this->get(route('careers.index'))
            ->assertOk()
            ->assertSee('We don&rsquo;t have a live vacancy', false);
    }

    public function test_the_fallback_is_replaced_by_the_listing_once_a_role_is_open(): void
    {
        JobOpening::factory()->create(['title' => ['en' => 'Quality Inspector']]);

        $this->get(route('careers.index'))
            ->assertOk()
            ->assertSee('Quality Inspector')
            ->assertDontSee('We don&rsquo;t have a live vacancy', false);
    }

    /**
     * CLAUDE.md §6/§8: no stock photography, and no invented names or
     * unconfirmed claims about company policy. These sections exist in the
     * markup but stay silent until their content methods return data —
     * same pattern AboutPageTest defends for milestones/leadership.
     */
    public function test_benefits_culture_photos_and_testimonials_are_hidden_while_unverified(): void
    {
        $html = $this->get(route('careers.index'))->getContent();

        $this->assertStringNotContainsString('What you get.', $html);
        $this->assertStringNotContainsString('Life at Radix', $html);
        $this->assertStringNotContainsString('In their own words.', $html);
    }

    public function test_the_application_forms_position_field_only_lists_open_roles(): void
    {
        $open = JobOpening::factory()->create(['title' => ['en' => 'Open Role Here']]);
        $closed = JobOpening::factory()->closed()->create(['title' => ['en' => 'Closed Role Here']]);

        $response = $this->get(route('careers.index'))->assertOk();

        $response->assertSee('value="'.$open->slug.'"', false);
        $response->assertDontSee('value="'.$closed->slug.'"', false);
    }
}
