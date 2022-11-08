<?php

return [
    'public_key' => env('UPLOAD_IO_PUBLIC_KEY', 'null'),
    'secret_key' => env('UPLOAD_IO_SECRET_KEY', 'null'),
    'account_id' => env('UPLOAD_IO_ACCOUNT_ID', 'null'),

    // @see https://upload.io/dashboard/transformations
    'transformations' => [
        'raw',
        'original',
        'medium',
        'thumbnail',
        'thumbnail-square',
    ],
];
