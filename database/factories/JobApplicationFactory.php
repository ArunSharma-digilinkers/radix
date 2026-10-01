<?php

namespace Database\Factories;

use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'job_opening_id' => null,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('98########'),
            'resume_disk' => 'local',
            'resume_path' => 'job-applications/'.fake()->uuid().'/resume.pdf',
            'cover_note' => fake()->sentence(12),
            'status' => JobApplication::STATUS_NEW,
        ];
    }
}
