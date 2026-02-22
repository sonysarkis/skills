<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Sonysarkis\Skills\Facades\Skills;

Route::get('/quotes-ui', function () {
    return view('skills::app');
});

Route::get('/api/quotes', function () {
    $quotes = Cache::get('quotes_list', []);
    return response()->json($quotes);
});

Route::get('/api/quotes/{id}', function ($id) {
    try {
        $quote = Skills::getQuote((int) $id);
        return response()->json($quote);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 429);
    }
});