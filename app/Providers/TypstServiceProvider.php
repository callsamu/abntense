<?php

namespace App\Providers;

use App\Services\TypstService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TypstServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(
            TypstService::class,
            fn (Application $app) => new TypstService()
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $result = Process::run("typst --version");

        if ($result->failed()) {
            throw new \Exception("Typst binary was not found");
        }

        Log::info("Typst --version: " . $result->output());
    }
}
