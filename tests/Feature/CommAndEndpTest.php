<?php

use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sonysarkis\Skills\Exceptions\RateLimitExceededException;
use Sonysarkis\Skills\Http\Integrations\DummyJson\Requests\GetQuoteRequest;

it('fetches a quote from the endpoint and uses binary search/cache', function () {
    Cache::clear();

    MockClient::global([
        GetQuoteRequest::class => MockResponse::make([
            'id' => 5, 
            'quote' => 'This is a test quote', 
            'author' => 'Test Author'
        ], 200),
    ]);

    $response = $this->getJson('/api/quotes/5');

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'id' => 5, 
                 'quote' => 'This is a test quote'
             ]);
});

it('runs the batch import command successfully', function () {
    Cache::clear();
    
    MockClient::global([
        GetQuoteRequest::class => MockResponse::make([
            'id' => 1, 
            'quote' => 'Command Test Quote', 
            'author' => 'Command Author'
        ], 200),
    ]);

    $this->artisan('quotes:batch-import', ['count' => 1])
         ->assertExitCode(0); 
});

it('retries the batch import command after rate limit is exceeded', function () {
    Cache::clear();
    config()->set('quotes.time_window', 0);

    $fakeSkills = new class {
        public int $calls = 0;

        public function getQuote(int $id): array
        {
            $this->calls++;

            if ($this->calls === 1) {
                throw new RateLimitExceededException();
            }

            $quotes = Cache::get('quotes_list', []);
            $quotes[] = [
                'id' => $id,
                'quote' => 'Recovered after rate limit',
                'author' => 'Test Author',
            ];

            Cache::put('quotes_list', $quotes);

            return end($quotes);
        }
    };

    $this->app->instance('skills', $fakeSkills);

    $this->artisan('quotes:batch-import', ['count' => 1])
        ->assertExitCode(0);

    expect($fakeSkills->calls)->toBe(2);
});

afterEach(function () {
    MockClient::destroyGlobal();
});