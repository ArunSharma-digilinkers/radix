<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'quote' => ['en' => fake()->sentence(18)],
            'author_name' => fake()->name(),
            'author_role' => ['en' => fake()->jobTitle()],
            'location' => fake()->city(),
            'type' => Testimonial::TYPE_RETAIL,
            'is_featured' => false,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function ofType(string $type): static
    {
        return $this->state(['type' => $type]);
    }
}
