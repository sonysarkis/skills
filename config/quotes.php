<?php

return [
  
    'base_url' => env('DUMMY_JSON_BASE_URL', 'https://dummyjson.com'),

    // settings 
    'request_limit' => (int) env('QUOTES_REQUEST_LIMIT', 5),
    'time_window'   => (int) env('QUOTES_TIME_WINDOW', 30),
];