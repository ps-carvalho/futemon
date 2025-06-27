<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class PlayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sports_monk_id' => fake()->numberBetween(1, 5000),
            'position_id' => fake()->numberBetween(1, 5000),
            'country_id' => fake()->numberBetween(1, 5000),
            'name' => fake()->name('male'),
            'common_name' => fake()->name('male'),
            'display_name' => fake()->name('male'),
            'gender' => 'male',
            'height' => fake()->numberBetween(120, 220),
            'weight' => fake()->numberBetween(50, 120),
            'date_of_birth' => '2000-01-01',
            'image_path' => 'https://cdn.sportmonks.com/images/soccer/players/14/14.png',
        ];
    }
}
