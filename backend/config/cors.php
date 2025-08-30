<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173,http://localhost:5174,http://127.0.0.1:5173,http://127.0.0.1:5174'))),

    'allowed_origins_patterns' => [
        // Matches: https://easy-pass-ems.vercel.app, https://easy-pass-ems-git-main-riths-projects-a27c1136.vercel.app
        '/^https:\/\/easy-pass-ems.*\.vercel\.app$/',
        // Matches: https://easy-pass-f6ad316mh-riths-projects-a27c1136.vercel.app, https://easy-pass-ems-git-main-riths-projects-a27c1136.vercel.app
        '/^https:\/\/.*-riths-projects-a27c1136\.vercel\.app$/',
    ],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],

    'exposed_headers' => [
        'X-CSRF-TOKEN',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,

];