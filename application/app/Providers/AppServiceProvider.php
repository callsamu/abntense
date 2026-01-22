<?php

namespace App\Providers;

use App\Services\TypstCompiler;
use App\Services\TypstConversor;
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
            TypstConversor::class,
            fn (Application $app) => new TypstConversor()
        );

        $this->app->singleton(
            TypstCompiler::class,
            fn (Application $app) => new TypstCompiler()
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
