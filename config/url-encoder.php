<?php
	
	return [
		/*
		|--------------------------------------------------------------------------
		| Enabled Route Groups
		|--------------------------------------------------------------------------
		|
		| Route groups where URL encoding should be automatically applied
		|
		*/
		'enable_route_groups' => [
			'web',
			'api',
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
		| Manual Middleware Registration
		|--------------------------------------------------------------------------
		|
		| Manually specify where to apply the middleware if not using route groups
		| Options: 'global', 'none', or array of specific middleware groups
		|
		*/
		'manual_middleware_registration' => 'auto',
		
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