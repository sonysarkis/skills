<?php

namespace Sonysarkis\Skills;

use Illuminate\Support\ServiceProvider;
use Sonysarkis\Skills\Services\QuoteService;
use Sonysarkis\Skills\Http\Integrations\DummyJson\DummyJsonConnector;


class SkillsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    // archive the config file
        $this->mergeConfigFrom(__DIR__.'/../config/quotes.php', 'quotes');

        $this->app->bind('skills', function ($app) {
            return new QuoteService(new DummyJsonConnector());
        });
    }

    
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {$this->publishes([
                __DIR__.'/../config/quotes.php' => 
                config_path('quotes.php'),
            ], 'quotes-config');

            $this->commands([
                \Sonysarkis\Skills\Console\Commands\ImportQuotesCommand::class,
            ]);
        }
    }
}