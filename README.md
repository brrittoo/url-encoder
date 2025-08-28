# Laravel URL Encoder
[![Latest Version](https://img.shields.io/packagist/v/brrittoo/url-encoder.svg?style=flat-square)](https://packagist.org/packages/paramguard/url-encoder)
[![Total Downloads](https://img.shields.io/packagist/dt/brrittoo/url-encoder.svg?style=flat-square)](https://packagist.org/packages/brrittoo/url-encoder)
[![License](https://img.shields.io/packagist/l/brrittoo/url-encoder.svg?style=flat-square)](https://packagist.org/packages/brrittoo/url-encoder)

Secure URL parameter encryption for Laravel applications. Protect sensitive data in your routes with AES-128-CTR encryption.

## Features

- Automatic encryption/decryption of route parameters
- Seamless integration with Laravel's URL generation
- AES-128-CTR encryption with random IVs
- URL-safe base64 encoding
- Configurable route groups and exclusions
- JavaScript compatibility
- Optional full source publish for local development

---

## Installation

Install via Composer:

```bash
composer require brrittoo/url-encoder
```

Publish the config file:

```bash
php artisan vendor:publish --provider="Brrittoo\UrlEncoder\UrlEncoderServiceProvider" --tag="url-encoder-config"
```

---

## Configuration

Set these in your `.env`:

```dotenv
URL_ENCODE_ENABLE=true
URL_ENCRYPTION_SECRET_KEY=your-32-character-secret-key-here
```



Edit `config/url-encoder.php` to customize:

```php
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
```

## Manual Control the middleware
If you want full control, change in config/url-encoder.php:
````
'manual_middleware_registration' => 'none',
````

Add the middleware alias to your `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // Other middleware...
    'url-encode' => \Brrittoo\UrlEncoder\Middleware\UrlManipulationMiddleware::class,
];
```
you can also use like this way

```php
protected $middlewareGroups = [
    'web' => [
        // existing middleware...
        \Brrittoo\UrlEncoder\Middleware\UrlManipulationMiddleware::class,
    ],
];
```

---

## Usage

Config-cache Command:

```bash
php artisan config:cache
```

### Basic Usage

```php
Route::get('/project/{project_id}/task/{task_id}', function ($project_id, $task_id) {
    // Parameters are automatically decrypted
})->name('project.task.show');
```


### JavaScript Integration

**PHP Side (Controller/Blade):**
```php
$urlData = encryptedRoute('project.task.show', ['project_id', 'task_id']);
```

**JavaScript Side:**
```html
<script>
   let action_url = @json($urlData['url'])
           .replace(@json($urlData['placeholders']['project_id']), projectId)
           .replace(@json($urlData['placeholders']['task_id']), taskId);

   fetch(action_url)
           .then(response => response.json())
           .then(data => console.log(data));
</script>
```

---

### Methods

- `encryptedRouteJS(string $routeName, array $paramNames)`

```php
$urlData = encryptedRouteJS('route.name', ['param1', 'param2']);
// Returns: ['url' => '...', 'placeholders' => [...]]
```

---

## License

This package is open-source and licensed under the MIT License.


## Author

**Ataul Galib**  
Email: ataul.gonigalib@gmail.com

