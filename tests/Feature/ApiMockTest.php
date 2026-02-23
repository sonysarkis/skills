<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sonysarkis\Skills\Http\Integrations\DummyJson\DummyJsonConnector;
use Sonysarkis\Skills\Http\Integrations\DummyJson\Requests\GetQuoteRequest;

if (!function_exists('config')) {
    function config($key, $default = null) {
        if ($key === 'quotes.base_url') {
            return 'https://dummyjson.com';
        }
        return $default;
    }
}

it('simulates the DummyJSON API and returns a fake quote (Mocking)', function () {
    $mockClient = new MockClient([
        GetQuoteRequest::class => MockResponse::make([
            'id' => 999, 
            'quote' => 'This is a fake quote simulated by Pest', 
            'author' => 'Test Author'
        ], 200),
    ]);

    $connector = new DummyJsonConnector();
    $connector->withMockClient($mockClient);

    $response = $connector->send(new GetQuoteRequest(999));

    expect($response->status())->toBe(200)
        ->and($response->json('id'))->toBe(999)
        ->and($response->json('quote'))->toBe('This is a fake quote simulated by Pest');
});