<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true).' Batteries';

        return [
            'slug' => Str::slug($name),
            // Translatable columns take the full locale map, not a bare string.
            'name' => ['en' => $name],
            'pitch' => ['en' => fake()->sentence()],
            'description' => ['en' => fake()->paragraph()],
            'kind' => Product::KIND_BATTERY,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
            'is_featured' => false,
        ];
    }

    public function solarSystem(): static
    {
        return $this->state(['kind' => Product::KIND_SOLAR_SYSTEM]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
