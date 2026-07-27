<?php

namespace Database\Factories;

use App\Models\Dealer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dealer>
 */
class DealerFactory extends Factory
{
    protected $model = Dealer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Battery House',
            'type' => Dealer::TYPE_RETAIL,
            'phone' => fake()->numerify('98########'),
            'address_line' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => 'Uttar Pradesh',
            'pincode' => fake()->numerify('2#####'),
            'country_code' => 'IN',
            'latitude' => fake()->latitude(8, 35),
            'longitude' => fake()->longitude(68, 97),
            'is_active' => true,
        ];
    }

    /** Places a dealer at an exact point, for distance assertions. */
    public function at(float $latitude, float $longitude): static
    {
        return $this->state(['latitude' => $latitude, 'longitude' => $longitude]);
    }

    /** Some of the client's records may arrive without coordinates. */
    public function withoutCoordinates(): static
    {
        return $this->state(['latitude' => null, 'longitude' => null]);
    }

    public function distributor(): static
    {
        return $this->state(['type' => Dealer::TYPE_DISTRIBUTOR]);
    }
}
