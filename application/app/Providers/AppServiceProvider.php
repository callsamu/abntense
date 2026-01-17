<?php

namespace App\Providers;

use App\Services\TypstCompilerService;
use App\Services\TypstConversorService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            TypstConversorService::class,
            fn (Application $app) => new TypstConversorService()
        );

        $this->app->singleton(
            TypstCompilerService::class,
            fn (Application $app) => new TypstCompilerService()
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
