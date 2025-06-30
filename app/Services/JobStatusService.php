<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\IJobStatusService;
use Illuminate\Support\Facades\Cache;

final class JobStatusService implements IJobStatusService
{
    private const SETUP_JOB_KEY = 'setup_job_completed';

    private const APP_SETUP_KEY = 'app_setup_is_completed';

    public function markJobCompleted(): void
    {
        Cache::put(self::SETUP_JOB_KEY, true);
    }

    public function isJobCompleted(): bool
    {
        return Cache::has(self::SETUP_JOB_KEY);
    }

    public function isSetupCompleted(): bool
    {
        return Cache::has(self::APP_SETUP_KEY);
    }

    public function markAppSetupCompleted(): void
    {
        Cache::forget(self::SETUP_JOB_KEY);
        Cache::put(self::APP_SETUP_KEY, true);
    }
}
