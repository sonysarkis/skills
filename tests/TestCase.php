<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Sonysarkis\Skills\SkillsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SkillsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        config()->set('quotes.base_url', 'https://dummyjson.com');
        config()->set('quotes.request_limit', 5);
        config()->set('quotes.time_window', 30);
    }
}