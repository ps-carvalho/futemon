<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Contracts\Services\IJobStatusService;
use App\Jobs\ImportSportsMonksDataJob;
use App\Jobs\SeedMockedDataJob;
use Exception;
use Illuminate\View\View;
use Livewire\Component;

final class SetupComponent extends Component
{
    public bool $isProcessing = false;

    public string $processingMessage = '';

    public function seedWithMockedData(): void
    {
        try {

            $this->isProcessing = true;
            $this->processingMessage = 'Seeding with mocked data...';

            // Dispatch the job
            SeedMockedDataJob::dispatch()
                ->onQueue('default')
                ->afterCommit();

            // Poll for job completion
            $this->dispatch('start-job-polling');
        } catch (Exception $exception) {
            $this->isProcessing = false;
            $this->addError('general', 'Failed to start seeding process: '.$exception->getMessage());
        }

    }

    public function seedWithSportsMonksData(): void
    {
        try {
            $this->isProcessing = true;
            $this->processingMessage = 'Importing data from SportsMonks API...';

            // Dispatch the job
            ImportSportsMonksDataJob::dispatch()
                ->onQueue('default')
                ->afterCommit();

            // Poll for job completion
            $this->dispatch('start-job-polling');
        } catch (Exception $exception) {
            $this->isProcessing = false;
            $this->addError('general', 'Failed to start seeding process: '.$exception->getMessage());
        }
    }

    public function checkJobStatus(): void
    {
        if ($this->isJobCompleted()) {
            $this->redirect('/players');
        }
    }

    public function render(): View
    {
        return view('livewire.setup-component');
    }

    private function isJobCompleted(): bool
    {
        $completed = app(IJobStatusService::class)->isJobCompleted();

        if (! $completed) {
            return false;
        }

        app(IJobStatusService::class)->markAppSetupCompleted();

        return true;
    }
}
