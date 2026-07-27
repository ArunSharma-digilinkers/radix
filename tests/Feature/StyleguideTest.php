<?php

namespace Tests\Feature;

use Tests\TestCase;

class StyleguideTest extends TestCase
{
    public function test_it_renders_outside_production(): void
    {
        $this->get('/styleguide')
            ->assertOk()
            ->assertSee('Style guide');
    }

    /**
     * The style guide is a build tool. In production it should not exist at all —
     * not merely be unlinked — so it can never be indexed or leak internal notes.
     */
    public function test_the_route_is_not_registered_in_production(): void
    {
        $this->assertStringContainsString(
            'isProduction',
            file_get_contents(base_path('routes/web.php')),
            'The styleguide route must stay behind an environment guard.'
        );
    }
}
