<?php

declare(strict_types=1);

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Log;

final class SeedMockedDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
            Cache::put('data_populated', true, now()->addMinutes(2400));

        } catch (Exception $exception) {
            // Handle error
            Log::error('Mocked data seeding failed: '.$exception->getMessage());
            throw $exception;
        }
    }
}
