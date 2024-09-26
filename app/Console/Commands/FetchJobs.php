<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JobProviderFactory;
use App\Models\Job;

class FetchJobs extends Command
{
    protected $signature = 'jobs:fetch';
    protected $description = 'Fetch jobs from providers';

    public function handle()
    {
        $providers = ['provider1', 'provider2'];

        foreach ($providers as $provider) {
            $jobProvider = JobProviderFactory::create($provider);
            $jobs = $jobProvider->fetchJobs();

            foreach ($jobs as $job) {
                Job::updateOrCreate(['title' => $job['title']], [
                    'time' => $job['time'], 
                    'level' => $job['level']
                ]);
            }
        }

        $this->info('Success!');
    }
}
