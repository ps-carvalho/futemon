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

            Cache::put('setup_job_completed', true, now()->addMinutes(10));
            Cache::put('app_setup_is_completed', true);

        } catch (Exception $exception) {
            // Handle error
            Log::error('Mocked data seeding failed: '.$exception->getMessage());
            throw $exception;
        }
    }
}
