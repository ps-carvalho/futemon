<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SeedMockedDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        try {
            // Run your database seeder
            Artisan::call('db:seed');

            // Mark job as completed
            Cache::put('setup_job_completed_mocked_data', true, now()->addMinutes(10));

        } catch (\Exception $e) {
            // Handle error
            \Log::error('Mocked data seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
