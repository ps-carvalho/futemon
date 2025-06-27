<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class CountryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'England',
            'official_name' => 'England',
            'iso2' => 'ENG',
            'iso3' => 'ENG',
            'image_path' => 'https://cdn.sportmonks.com/images/flags/16x11/eng.png',
            'fifa_name' => 'ENG',
            'latitude' => '51.507222',
            'longitude' => '-0.1275',
        ];
    }
}
