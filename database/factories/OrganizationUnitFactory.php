<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrganizationUnitType;
use App\Models\OrganizationUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<OrganizationUnit>
 */
class OrganizationUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'type' => fake()->randomElement(OrganizationUnitType::cases())->value,
            'name' => $name,
            'slug' => Str::slug($name . '-' . fake()->unique()->numberBetween(1, 9999)),
            'acronym' => strtoupper(fake()->lexify('???')),
            'tagline' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'logo' => null,
            'chairperson' => fake()->name(),
            'secretary' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'website' => fake()->url(),
            'address' => fake()->address(),
            'meta' => [],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
