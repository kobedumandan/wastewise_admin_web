<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Contract\Database as FirebaseDatabase;
use Kreait\Firebase\Contract\Storage as FirebaseStorage;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('firebase', function ($app) {
            $credentialsPath = config('firebase.credentials');
            
            // Check if credentials file exists
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
            }

            return (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri(config('firebase.database_url'));
        });

        $this->app->singleton(FirebaseAuth::class, function ($app) {
            return $app->make('firebase')->createAuth();
        });

        $this->app->singleton(FirebaseDatabase::class, function ($app) {
            return $app->make('firebase')->createDatabase();
        });

        $this->app->singleton(FirebaseStorage::class, function ($app) {
            return $app->make('firebase')->createStorage();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

