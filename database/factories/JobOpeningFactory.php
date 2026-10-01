<?php

namespace Database\Factories;

use App\Models\JobOpening;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobOpening>
 */
class JobOpeningFactory extends Factory
{
    protected $model = JobOpening::class;

    public function definition(): array
    {
        $title = fake()->unique()->jobTitle();

        return [
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'title' => ['en' => $title],
            'department' => fake()->randomElement(['Production', 'Quality', 'Sales', 'Logistics']),
            'location' => 'Kanpur, Uttar Pradesh',
            'employment_type' => JobOpening::TYPE_FULL_TIME,
            'description' => ['en' => fake()->paragraph()],
            'requirements' => ['en' => fake()->paragraph()],
            'published_at' => now()->subDays(fake()->numberBetween(1, 20)),
            'closes_on' => null,
        ];
    }

    /** Written but not yet published — invisible on the public site. */
    public function draft(): static
    {
        return $this->state(['published_at' => null]);
    }

    /** Published, but past its own closing date — no longer accepting applicants. */
    public function closed(): static
    {
        return $this->state(['closes_on' => now()->subDay()->toDateString()]);
    }
}
