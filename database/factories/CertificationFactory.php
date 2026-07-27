<?php

namespace Database\Factories;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certification>
 */
class CertificationFactory extends Factory
{
    protected $model = Certification::class;

    public function definition(): array
    {
        return [
            'name' => ['en' => 'ISO '.fake()->numberBetween(9000, 9999).':2015'],
            'issuer' => 'Bureau of Indian Standards',
            'reference_no' => fake()->bothify('BIS-####-??'),
            'issued_on' => now()->subYears(2),
            'expires_on' => now()->addYear(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function expired(): static
    {
        return $this->state([
            'issued_on' => now()->subYears(5),
            'expires_on' => now()->subMonth(),
        ]);
    }

    public function neverExpires(): static
    {
        return $this->state(['expires_on' => null]);
    }
}
