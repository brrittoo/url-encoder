# Laravel URL Encoder
[![Latest Version](https://img.shields.io/packagist/v/paramguard/url-encoder.svg?style=flat-square)](https://packagist.org/packages/paramguard/url-encoder)
[![Total Downloads](https://img.shields.io/packagist/dt/paramguard/url-encoder.svg?style=flat-square)](https://packagist.org/packages/paramguard/url-encoder)
[![License](https://img.shields.io/packagist/l/paramguard/url-encoder.svg?style=flat-square)](https://packagist.org/packages/paramguard/url-encoder)

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
composer require paramguard/url-encoder
```

Publish the config file:

```bash
php artisan vendor:publish --provider="ParamGuard\UrlEncoder\UrlEncoderServiceProvider" --tag="url-encoder-config"
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
    | Middleware Alias
    |--------------------------------------------------------------------------
    */
    'middleware_alias' => 'url-encode',

    /*
    |--------------------------------------------------------------------------
    | Enabled Route Groups
    |--------------------------------------------------------------------------
    */
    'enable_route_groups' => [
        'web'
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Routes
    |--------------------------------------------------------------------------
    */
    'exclude_routes' => [
        'login',
        'register'
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable Encoding
    |--------------------------------------------------------------------------
    */
    'is_encoding_enable' => env('URL_ENCODE_ENABLE', false),

    /*
    |--------------------------------------------------------------------------
    | Encryption Secret Key
    |--------------------------------------------------------------------------
    */
    'url_encryption_secret_key' => env('URL_ENCRYPTION_SECRET_KEY'),
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

## Local Development (Full Source Access)

If a developer needs to modify the package code locally, the package provides an **optional source publish**:

In `UrlEncoderServiceProvider`:
```php
$this->publishes([
    __DIR__ . '/../' => base_path('packages/paramguard/url-encoder'),
], 'url-encoder-source');
```

Publish the source:
```bash
php artisan vendor:publish --tag=url-encoder-source
```

This will copy the full package into:
```
packages/paramguard/url-encoder
```

To use the local version, update your `composer.json` with the following repository entry:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/paramguard/url-encoder"
    }
]
```

Then run:

```bash
composer update paramguard/url-encoder
```

This will local package so changes take effect immediately without needing to reinstall.



## Local Development (From Git Clone)


If you prefer to clone the package repo directly and develop locally with a symlink:

Clone the package repo outside your Laravel project folder:

```bash
git clone https://github.com/ataulgalib/laravel-url-encoder.git ../laravel-url-encoder
```


Add this repository to your Laravel project’s composer.json:

```json
      "repositories": [
         {
            "type": "path",
            "url": "../laravel-url-encoder",
            "options": {
                "symlink": true
            }
         }
    ]
```


Require the package locally using the dev version:

```bash
composer require paramguard/url-encoder:@dev
```


Publish the package config:

```bash
php artisan vendor:publish --provider="ParamGuard\UrlEncoder\UrlEncoderServiceProvider" --tag="url-encoder-config"
```

This symlinks the package source from your cloned repo into your Laravel project, so any changes in the clone take effect immediately.

---

## Contributing

1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature/your-feature
   ```
3. Make changes and commit:
   ```bash
   git commit -m "Description of change"
   ```
4. Push to your fork:
   ```bash
   git push origin feature/your-feature
   ```
5. Submit a Pull Request.

We’ll review your contribution together before merging.

---

## License

This package is open-source and licensed under the MIT License.
