<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProviderTwoService implements JobProviderInterface
{
    public function fetchJobs()
    {
        return Http::get('https://api.provider2.com/jobs')->json();
    }
}
