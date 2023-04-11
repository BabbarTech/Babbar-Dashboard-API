<?php

use App\Models\Gateway;

return [
    Gateway::BABBAR => [
        'label' => 'Babbar.tech API',
        'required' => true,
        'default_rate_limit_per_minute' => 6,
        'base_url' => env('BABBAR_BASE_URL', 'https://www.babbar.tech/api/'),
    ],

    Gateway::YTG => [
        'label' => 'Yourtext.Guru API',
        'required' => false,
        'default_rate_limit_per_minute' => 5,
        'base_url' => env('YTG_BASE_URL', 'https://yourtext.guru/api/'),
    ],

    Gateway::TRAFILATURA => [
        'label' => 'Trafilatura API',
        'required' => false,
        'default_rate_limit_per_minute' => 5,
        'base_url' => env('TRAFILATURA_BASE_URL', 'http://trafilatura:5000/'),
    ],
];
