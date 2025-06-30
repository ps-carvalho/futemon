<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\IJobStatusService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

final class SeedMockedDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(IJobStatusService $jobStatusService): void
    {
        try {
            // Run your database seeder
            Artisan::call('db:seed');

            $jobStatusService->markJobCompleted();

        } catch (Exception $exception) {
            // Handle error
            Log::error('Mocked data seeding failed: '.$exception->getMessage());
            throw $exception;
        }
    }
}
