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
use Log;

final class ImportSportsMonksDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function handle(IImportService $sportsMonksService): void
    {
        try {
            $runs = 100;
            $i = 50;
            while ( $i < $runs)
            {
                $i++;
                // Import players and teams from SportsMonks API
                $sportsMonksService->importPlayers($i);

            }
            if($i == $runs) {
                // Mark job as completed
                Cache::put('setup_job_completed_sportsmonks_data', true, now()->addMinutes(10));
            }

        } catch (Exception $e) {
            // Handle error
            Log::error('SportsMonks data import failed: '.$e->getMessage());
            throw $e;
        }
    }
}
