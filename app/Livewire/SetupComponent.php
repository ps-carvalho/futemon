<?php

namespace App\Livewire;

use Livewire\Component;
use App\Jobs\SeedMockedDataJob;
use App\Jobs\ImportSportsMonksDataJob;
use Illuminate\Support\Facades\Session;

class SetupComponent extends Component
{
    public $isProcessing = false;
    public $jobStatus = null;
    public $processingMessage = '';

    public function seedWithMockedData()
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

    public function seedWithSportsMonksData()
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

    public function checkJobStatus()
    {
        // Check if job is completed (you can implement your own logic here)
        // For example, check a cache key or database flag set by the job
        $jobType = Session::get('setup_job_type');

        if ($jobType && $this->isJobCompleted($jobType)) {
            Session::forget(['setup_job_type', 'setup_job_started']);
            $this->redirect('/players');
        }
    }

    private function isJobCompleted($jobType)
    {
        // Implement your job completion check logic here
        // This could check a cache key, database flag, or file existence
        // For example:
        return cache()->has("setup_job_completed_{$jobType}");
    }

    public function render()
    {
        return view('livewire.setup-component');
    }
}
