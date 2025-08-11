<?php

    const ENCRYPTED_PARAM = 'encrypt';
    const DECRYPTED_PARAM = 'decrypt';


    if (!function_exists('enableUrlEncode')) {
        function enableUrlEncode()
        {
            return config('url-encoder.is_encoding_enable');
        }
    }
