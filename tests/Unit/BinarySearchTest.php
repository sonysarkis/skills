<?php
use Sonysarkis\Skills\Services\QuoteService;

it('correctly finds the index of an element using binary search', function () {
    $sortedList = [
        ['id' => 2, 'quote' => 'Quote A'],  
        ['id' => 5, 'quote' => 'Quote B'],  
        ['id' => 10, 'quote' => 'Quote C'], 
        ['id' => 15, 'quote' => 'Quote D'],
        ['id' => 20, 'quote' => 'Quote E'], 
    ];

    $reflection = new \ReflectionClass(QuoteService::class);
    
    $service = $reflection->newInstanceWithoutConstructor();
    
    $method = $reflection->getMethod('binarySearch'); 
    $method->setAccessible(true);

    $result = $method->invoke($service, $sortedList, 10);

    expect($result)->toBe(2);
});

it('returns null when the ID does not exist in the list', function () {
    $sortedList = [
        ['id' => 1, 'quote' => 'Quote A'],
        ['id' => 5, 'quote' => 'Quote B'],
    ];

    $reflection = new \ReflectionClass(QuoteService::class);
    $service = $reflection->newInstanceWithoutConstructor();
    
    $method = $reflection->getMethod('binarySearch');
    $method->setAccessible(true);

    $result = $method->invoke($service, $sortedList, 3);

    expect($result)->toBeNull();
});