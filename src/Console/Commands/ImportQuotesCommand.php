<?php

namespace Sonysarkis\Skills\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Sonysarkis\Skills\Facades\Skills;
use Sonysarkis\Skills\Exceptions\RateLimitExceededException;

class ImportQuotesCommand extends Command
{
    // command and description
    protected $signature = 'quotes:batch-import {count}';
    protected $description = 'Import a batch of unique quotes from the API, respecting rate limits and providing real-time feedback.';

    // command logic
    public function handle()
    {
        // read the count argument
        $count = (int) $this->argument('count');
        $this->info("Iniciating import of {$count} unique quotes...");
        $imported = 0;
        $currentId = 1; // we start from ID 1 

        while ($imported < $count) {
            try {
                // verify if we already have this quote
                $quotesList = Cache::get('quotes_list', []);
                
                $exists = in_array($currentId, array_column($quotesList, 'id'));
                if ($exists) {$currentId++;continue; }
                Skills::getQuote($currentId);
                $imported++;
                // feedback
                $this->line("Imported quote ID: {$currentId} ({$imported}/{$count})");
                $currentId++;

            } catch (RateLimitExceededException $e) {
                // time window to wait before retrying
                $window = config('quotes.time_window', 30);
                $this->warn("Rate limit exceeded. Waiting for {$window} seconds before retrying...");
                sleep($window);
                $this->info("Retrying now...");
                
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
                break;
            }
        }

        if ($imported === $count) {
            $this->info("Successfully imported {$imported} unique quotes!");
        }
    }
}