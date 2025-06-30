<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\IImportService;
use App\Contracts\Services\IJobStatusService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class ImportSportsMonksDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @throws Exception
     */
    public function handle(IImportService $importService, IJobStatusService $jobStatusService): void
    {
        try {
            $runs = 100;
            $i = 0;
            while ($i < $runs) {
                ++$i;
                $importService->importPlayers($i);
            }

            $jobStatusService->markJobCompleted();

        } catch (Exception $exception) {
            Log::error('SportsMonks data import failed: '.$exception->getMessage());
            throw $exception;
        }
    }
}
