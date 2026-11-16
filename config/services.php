<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Reading a timetable picture (admin Timetable page). Keys live in .env only.
    // TIMETABLE_READER: "gemini" (Google free tier, default) or "anthropic" (Claude, paid).
    'timetable_reader' => env('TIMETABLE_READER', 'gemini'),
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),
    ],
    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-5-5'),
        'fake' => (bool) env('ANTHROPIC_FAKE', false),   // local testing only: made-up rows, no internet
    ],

    // Zoom classes: the campus Zoom account (Server-to-Server OAuth app). Values live in .env only.
    'zoom' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'webhook_secret' => env('ZOOM_WEBHOOK_SECRET'),   // "Secret Token" of the app's event subscription
        'host' => env('ZOOM_HOST_EMAIL'),                 // Zoom login (email) that owns the meetings
        'timezone' => env('ZOOM_TIMEZONE', 'Asia/Colombo'),
        'fake' => (bool) env('ZOOM_FAKE', false),         // local testing only: pretend Zoom, no internet needed
        'retry_minutes' => 5,                             // wait between tries while Zoom prepares the list
        'max_attempts' => 12,
    ],

];
