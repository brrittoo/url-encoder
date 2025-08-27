<?php
	
	return [
		/*
		|--------------------------------------------------------------------------
		| Enable Encoding
		|--------------------------------------------------------------------------
		|
		| Master switch for URL encoding functionality
		|
		*/
		'is_encoding_enable' => env('URL_ENCODE_ENABLE', false),
		
		/*
		|--------------------------------------------------------------------------
		| Encryption Secret Key
		|--------------------------------------------------------------------------
		|
		| Key used for encrypting/decrypting URL parameters
		|
		*/
		'url_encryption_secret_key' => env('URL_ENCRYPTION_SECRET_KEY'),
	];
