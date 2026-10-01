<?php

namespace Database\Factories;

use App\Models\ExportMarket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ExportMarket>
 */
class ExportMarketFactory extends Factory
{
    protected $model = ExportMarket::class;

    public function definition(): array
    {
        $name = fake()->unique()->country();

        return [
            'slug' => Str::slug($name),
            'country_name' => ['en' => $name],
            'iso_code' => strtoupper(fake()->unique()->lexify('??')),
            'iso_numeric' => fake()->unique()->numberBetween(1, 894),
            'blurb' => ['en' => fake()->sentence(15)],
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
