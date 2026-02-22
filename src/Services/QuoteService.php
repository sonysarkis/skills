<?php

namespace Sonysarkis\Skills\Services;

use Illuminate\Support\Facades\Cache;
use Sonysarkis\Skills\Exceptions\RateLimitExceededException;
use Sonysarkis\Skills\Http\Integrations\DummyJson\DummyJsonConnector;
use Sonysarkis\Skills\Http\Integrations\DummyJson\Requests\GetQuoteRequest;

class QuoteService
{
    // api
    public function __construct(protected DummyJsonConnector $connector
    ) {}

    
    public function getQuote(int $id): array
    {
        // rate limit
        $this->checkRateLimit();

        // cache list
        $quotes = Cache::get('quotes_list', []);

        // binary search
        $index = $this->binarySearch($quotes, $id);

        if ($index !== null) {
            return $quotes[$index];
        }

        // if not found, fetch from API
        $response = $this->connector->send(new GetQuoteRequest($id));
        
        if ($response->failed()) {
            throw new \Exception("Error fetching quote with ID {$id} from API.");
        }

        $newQuote = $response->json();

        // cache the new quote
        $quotes[] = $newQuote;
        
        
        usort($quotes, fn($a, $b) => $a['id'] <=> $b['id']);
        
        Cache::put('quotes_list', $quotes);

        return $newQuote;
    }

   
    protected function checkRateLimit(): void
    {
        $limit = config('quotes.request_limit'); 
        $window = config('quotes.time_window'); 
        $key = 'quotes_api_hits';

        $hits = Cache::get($key, 0);

        if ($hits >= $limit) {
            throw new RateLimitExceededException();
        }

        // counter logic
        if ($hits === 0) {
            Cache::put($key, 1, $window);
        } else {
            Cache::increment($key);
        }
    }

    

    // algorithm for binary search
    protected function binarySearch(array $list, int $targetId): ?int
    {
        $low = 0;
        $high = count($list) - 1;

        while ($low <= $high) {
            $mid = (int) floor(($low + $high) / 2);
            $currentId = $list[$mid]['id'];

            if ($currentId === $targetId) {
                return $mid; 
            }

            if ($targetId < $currentId) {
                $high = $mid - 1; 
            } else {
                $low = $mid + 1; 
            }
        }

        return null; 
    }
}