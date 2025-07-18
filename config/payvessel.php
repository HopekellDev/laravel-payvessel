<?php

return [
    'api_key' => env('PAYVESSEL_API_KEY'),
    'api_secret' => env('PAYVESSEL_API_SECRET'),
    'business_id' => env('BUSINESS_ID'),
    'base_url' => env('PAYVESSEL_API_URL', 'https://api.payvessel.com'),
];
