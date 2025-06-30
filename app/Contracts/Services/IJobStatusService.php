<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface IJobStatusService
{
    public function markJobCompleted(): void;

    public function isJobCompleted(): bool;

    public function markAppSetupCompleted(): void;

    public function isSetupCompleted(): bool;
}
