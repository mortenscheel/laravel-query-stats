<?php

namespace MortenScheel\QueryStats;

use Illuminate\Support\ServiceProvider;

class QueryStatsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/query-stats.php', 'query-stats');

        // Register the service the package provides.
        $this->app->singleton('query-stats', function () {
            return new QueryStats;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array|string[]
     */
    public function provides(): array
    {
        return ['query-stats'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/query-stats.php' => config_path('query-stats.php'),
        ], 'query-stats.config');
    }
}
