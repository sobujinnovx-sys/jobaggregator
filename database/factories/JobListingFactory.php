<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobListingFactory extends Factory
{
    protected $model = JobListing::class;

    public function definition(): array
    {
        $titles = [
            'Laravel Developer', 'React Developer', 'Full Stack Developer',
            'Vue.js Developer', 'PHP Developer', 'Node.js Developer',
            'Frontend Developer', 'Backend Developer', 'DevOps Engineer',
            'Mobile Developer', 'Data Analyst', 'UI/UX Designer',
            'Product Manager', 'QA Engineer', 'Python Developer',
        ];

        return [
            'title' => fake()->randomElement($titles),
            'company_id' => Company::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id,
            'location_type' => fake()->randomElement(['remote', 'onsite', 'hybrid']),
            'location' => fake()->city() . ', ' . fake()->country(),
            'experience_level' => fake()->randomElement(['junior', 'mid', 'senior', 'lead']),
            'description' => fake()->paragraphs(3, true),
            'apply_link' => fake()->url(),
            'salary_range' => '$' . fake()->numberBetween(40, 80) . 'k - $' . fake()->numberBetween(80, 200) . 'k',
            'status' => fake()->randomElement(['pending', 'approved', 'approved', 'approved']),
            'source' => 'manual',
            'external_id' => null,
            'posted_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
