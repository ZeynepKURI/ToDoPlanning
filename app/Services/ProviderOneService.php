<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProviderOneService implements JobProviderInterface
{
    public function fetchJobs()
    {
        return Http::get('https://api.provider1.com/jobs')->json();
    }
}
