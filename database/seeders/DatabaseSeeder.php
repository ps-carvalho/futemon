<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Player;
use App\Models\PlayerPosition;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create some countries and positions first
        $countries = Country::factory(70)->create();
        $positions = PlayerPosition::factory(5)->create();

        // Create players with random existing relationships
        Player::factory(10000)->create([
            'country_id' => fn () => $countries->random()->id,
            'position_id' => fn () => $positions->random()->id,
        ]);

    }
}
