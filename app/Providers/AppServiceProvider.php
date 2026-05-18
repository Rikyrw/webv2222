<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $caBundle = env('HTTP_CA_BUNDLE');

        if (!$caBundle) {
            return;
        }

        if (!is_file($caBundle)) {
            Log::warning('HTTP_CA_BUNDLE is configured but the file does not exist.', [
                'path' => $caBundle,
            ]);

            return;
        }

        Http::globalOptions([
            'verify' => $caBundle,
        ]);
    }
}
