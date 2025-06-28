<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class CountryFactory extends Factory
{
    public function definition(): array
    {

        return [
            'imported_id' => fake()->numberBetween(1, 5000),
            'name' => ($country = fake()->country()),
            'official_name' => $country,
            'iso2' => ($code = fake()->countryCode()),
            'iso3' => $code,
            'image_path' => 'https://cdn.sportmonks.com/images/flags/16x11/eng.png',
            'fifa_name' => $country,
            'latitude' => '51.507222',
            'longitude' => '-0.1275',
        ];
    }
}
