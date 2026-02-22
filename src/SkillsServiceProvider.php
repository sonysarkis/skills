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

    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    $this->loadViewsFrom(__DIR__.'/../resources/views', 'skills');


        if ($this->app->runningInConsole()) {$this->publishes([
                __DIR__.'/../config/quotes.php' => 
                config_path('quotes.php'),
            ], 'quotes-config');

            $this->publishes([
                __DIR__.'/../dist' => public_path('vendor/skills'),
            ], 'quotes-assets');

            $this->commands([
                \Sonysarkis\Skills\Console\Commands\ImportQuotesCommand::class,
            ]);
        }
    }
}