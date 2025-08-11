<?php

    return [
        'is_encoding_enable' => env('URL_ENCODE_ENABLE', false),
        'encryption_method' => env('ENCRYPTION_METHOD', 'AES-256-CBC'),
        'encryption_secret_key' => env('ENCRYPTION_SECRET_KEY'),
        'encryption_fixed_iv' => env('ENCRYPTION_FIXED_IV'),
        'encryption_salt' => env('ENCRYPTION_FIXED_SALT')
    ];
