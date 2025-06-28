<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class PlayerPositionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'imported_id' => fake()->numberBetween(1, 5000),
            'name' => ($position = fake()->randomElement(['Goalkeeper', 'Defender', 'Midfielder', 'Forward'])),
            'code' => match ($position) {
                'Goalkeeper' => 'GK',
                'Defender' => 'DF',
                'Midfielder' => 'MF',
                'Forward' => 'FW',
            },
            'developer_name' => $position,
            'model_type' => fake()->randomElement(['position', 'role']),
            'stat_group' => fake()->optional()->word,
        ];
    }
}
