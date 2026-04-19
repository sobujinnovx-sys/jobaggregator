<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Vercel serverless: ensure /tmp directories exist for compiled views
        if (env('VERCEL')) {
            $viewPath = env('VIEW_COMPILED_PATH', '/tmp/views');
            if (!is_dir($viewPath)) {
                mkdir($viewPath, 0755, true);
            }
        }
    }
}
