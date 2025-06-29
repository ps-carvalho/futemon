<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Jobs\ImportSportsMonksDataJob;
use App\Jobs\SeedMockedDataJob;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;

final class SetupComponent extends Component
{
    public bool $isProcessing = false;

    public string $processingMessage = '';

    public function seedWithMockedData(): void
    {
        $this->isProcessing = true;
        $this->processingMessage = 'Seeding with mocked data...';

        // Dispatch the job
        SeedMockedDataJob::dispatch()
            ->onQueue('default')
            ->afterCommit();

        // Store job info in session to track completion
        Session::put('setup_job_type', 'mocked_data');
        Session::put('setup_job_started', true);

        // Poll for job completion
        $this->dispatch('start-job-polling');
    }

    public function seedWithSportsMonksData(): void
    {
        $this->isProcessing = true;
        $this->processingMessage = 'Importing data from SportsMonks API...';

        // Dispatch the job
        ImportSportsMonksDataJob::dispatch()
            ->onQueue('default')
            ->afterCommit();

        // Store job info in session to track completion
        Session::put('setup_job_type', 'sportsmonks_data');
        Session::put('setup_job_started', true);

        // Poll for job completion
        $this->dispatch('start-job-polling');
    }

    public function checkJobStatus(): void
    {
        $jobType = Session::get('setup_job_type');
        if ($jobType && $this->isJobCompleted($jobType)) {
            Session::forget(['setup_job_type', 'setup_job_started']);
            $this->redirect('/players');
        }
    }

    public function render(): View
    {
        return view('livewire.setup-component');
    }

    private function isJobCompleted(string $jobType): bool
    {
        return cache()->has('setup_job_completed_'.$jobType);
    }
}
