<?php
	
	return [
		/*
		|--------------------------------------------------------------------------
		| Middleware Alias
		|--------------------------------------------------------------------------
		|
		| The alias to use for the URL encoding middleware
		|
		*/
		'middleware_alias' => 'url-encode',
		
		/*
		|--------------------------------------------------------------------------
		| Enabled Route Groups
		|--------------------------------------------------------------------------
		|
		| Route groups that should automatically have URL encoding middleware applied
		|
		*/
		'enable_route_groups' => [
			'web'
		],
		
		/*
		|--------------------------------------------------------------------------
		| Excluded Routes
		|--------------------------------------------------------------------------
		|
		| Route names that should skip URL encoding
		|
		*/
		'exclude_routes' => [
			'login',
			'register'
		],
		
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
		'encryption_secret_key' => env('URL_ENCRYPTION_SECRET_KEY'),
	];
