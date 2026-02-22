<?php

namespace Sonysarkis\Skills\Http\Integrations\DummyJson;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class DummyJsonConnector extends Connector
{
    use AcceptsJson;

    
    public function resolveBaseUrl(): string
    {
        return config('quotes.base_url');
    }

    protected function defaultConfig(): array
    {
        return [
            // verification Ssl off
            'verify' => false, 
        ];
    }
}