<?php

// config for Upcoach/UpstartForLaravel
return [
    'app_id' => env('UPCOACH_APP_ID'),
    'signing_secret' => env('UPCOACH_APP_SIGNING_SECRET'),
    'api_url' => env('UPCOACH_API_URL', 'https://api.upcoach.com'),
    'allow_all_domains_as_ancestors_on_blocks' => env('UPCOACH_ALLOW_ALL_DOMAINS_AS_ANCESTORS_ON_BLOCKS', true),
];
