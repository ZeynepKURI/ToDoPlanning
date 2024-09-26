<?php
namespace App\Services;

class JobProviderFactory
{
    public static function create($provider)
    {
        switch ($provider) {
            case 'provider1':
                return new ProviderOneService();
            case 'provider2':
                return new ProviderTwoService();
    
            default:
                throw new \Exception("Provider not found.");
        }
    }
}
