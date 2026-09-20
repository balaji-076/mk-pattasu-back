<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://www.kmvfireworks.sbs',
        'https://kmvfireworks.sbs',
        'http://localhost:5173',
        'https://kmvfireworks.com',
        'https://react-frontend-production-6ffd.up.railway.app'
        
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
