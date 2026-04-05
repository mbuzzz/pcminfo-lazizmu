<?php

namespace Database\Factories;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'excerpt' => fake()->sentence(),
            'content' => '<p>' . fake()->paragraph() . '</p>',
            'status' => PageStatus::Draft,
            'published_at' => null,
        ];
    }
}
