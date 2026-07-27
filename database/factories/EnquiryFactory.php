<?php

namespace Database\Factories;

use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    protected $model = Enquiry::class;

    public function definition(): array
    {
        return [
            'type' => Enquiry::TYPE_GENERAL,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('98########'),
            'city' => fake()->city(),
            'message' => fake()->sentence(12),
            'status' => Enquiry::STATUS_NEW,
        ];
    }

    public function ofType(string $type): static
    {
        return $this->state(['type' => $type]);
    }

    public function responded(): static
    {
        return $this->state([
            'responded_at' => now()->subHour(),
            'status' => Enquiry::STATUS_CONTACTED,
        ]);
    }

    /** Older than the one-business-day reply the brief promises. */
    public function stale(): static
    {
        return $this->state([
            'created_at' => now()->subDays(3),
            'responded_at' => null,
        ]);
    }
}
