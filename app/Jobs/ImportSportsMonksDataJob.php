<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\IImportService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
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
    public function handle(IImportService $importService): void
    {
        try {
            $runs = 50;
            $i = 0;
            while ($i < $runs) {
                ++$i;
                $importService->importPlayers($i);
            }

            Cache::put('setup_job_completed', true, now()->addMinutes(10));

        } catch (Exception $exception) {
            Log::error('SportsMonks data import failed: '.$exception->getMessage());
            throw $exception;
        }
    }
}
