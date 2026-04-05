<?php

namespace Database\Factories;

use App\Enums\PostReactionType;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostReaction>
 */
class PostReactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => function (): int {
                $author = User::factory()->create();
                $category = Category::query()->create([
                    'name' => fake()->words(2, true),
                    'slug' => fake()->unique()->slug(),
                    'type' => 'post',
                    'is_active' => true,
                ]);

                return Post::query()->create([
                    'category_id' => $category->getKey(),
                    'author_id' => $author->getKey(),
                    'title' => fake()->sentence(),
                    'slug' => fake()->unique()->slug(),
                    'excerpt' => fake()->sentence(),
                    'content' => '<p>' . fake()->paragraph() . '</p>',
                    'type' => 'news',
                    'status' => 'published',
                    'published_at' => now(),
                ])->getKey();
            },
            'user_id' => fn (): int => User::factory()->create()->getKey(),
            'type' => fake()->randomElement(PostReactionType::values()),
        ];
    }
}
