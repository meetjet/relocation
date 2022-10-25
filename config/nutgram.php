<?php

return [
    // The Telegram BOT api token (default)
    'token' => env('TELEGRAM_BOT_TOKEN'),
    'log_channel' => env('TELEGRAM_BOT_LOG_CHANNEL', 'null'),

    // The Telegram BOT api token (Armenia)
    'token_armenia' => env('TELEGRAM_BOT_TOKEN_ARMENIA'),
    'log_channel_armenia' => env('TELEGRAM_BOT_LOG_CHANNEL_ARMENIA', 'null'),

    // The Telegram BOT api secret
    'secret' => env('TELEGRAM_BOT_SECRET'),

    // if the webhook mode must validate the incoming IP range is from a telegram server
    'safe_mode' => env('APP_ENV', 'local') === 'production',

    // Extra or specific configurations
    'config' => [],

    // Set if the service provider should automatically load
    // handlers from /routes/telegram.php
    'routes' => false,

    // Enable or disable Nutgram mixins
    'mixins' => false,

    // Path to save files generated by nutgram:make command
    'namespace' => app_path('Telegram'),
];
