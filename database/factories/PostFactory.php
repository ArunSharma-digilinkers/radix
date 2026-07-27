<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'slug' => Str::slug($title),
            'title' => ['en' => $title],
            'excerpt' => ['en' => fake()->sentence(15)],
            'body' => ['en' => fake()->paragraphs(3, true)],
            'published_at' => now()->subDays(fake()->numberBetween(1, 90)),
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(['published_at' => null]);
    }

    /** Publish date in the future: written, approved, not yet visible. */
    public function scheduled(): static
    {
        return $this->state(['published_at' => now()->addWeek()]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
