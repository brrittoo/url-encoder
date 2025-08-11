
# Laravel URL Encoder

Secure URL parameter encryption for Laravel applications. Protect sensitive data in your routes with AES-128-CTR encryption.

## Features

- 🔒 Automatic encryption/decryption of route parameters
- 🔄 Seamless integration with Laravel's URL generation
- 🛡️ AES-128-CTR encryption with random IVs
- 🌐 URL-safe base64 encoding
- 🚦 Configurable route groups and exclusions
- 💻 JavaScript compatibility
- ✨ Blade directive support

## Installation

Install via Composer:

```bash
composer require paramguard/url-encoder
```

Publish the config file:

```bash
php artisan vendor:publish --provider="ParamGuard\UrlEncoder\UrlEncoderServiceProvider" --tag="url-encoder-config"
```

## Configuration

Set these in your `.env`:

```dotenv
URL_ENCODE_ENABLE=true
URL_ENCRYPTION_SECRET_KEY=your-32-character-secret-key-here
```

Edit `config/url-encoder.php` to customize:

```php
return [
    'middleware_alias' => 'url-encode',
    'enable_route_groups' => ['web'],
    'exclude_routes' => ['login', 'register'],
    'is_encoding_enable' => env('URL_ENCODE_ENABLE', false),
    'url_encryption_secret_key' => env('URL_ENCRYPTION_SECRET_KEY'),
];
```

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

### Blade Directives

```html
<!-- Encrypt -->
<a href="/project/@encrypt($project->id)">Secure Link</a>

<!-- Decrypt -->
<input type="hidden" name="token" value="@decrypt($encryptedToken)">
```

### JavaScript Integration

**PHP Side (Controller/Blade):**

```php
$urlData = encryptedRoute('project.task.show', ['project_id', 'task_id']);
```

**JavaScript Side:**

```html
<script>
// Get encrypted URL template with placeholders
let action_url = @json($urlData['url'])
    .replace(@json($urlData['placeholders']['project_id']), projectId)
    .replace(@json($urlData['placeholders']['task_id']), taskId);

// Example AJAX call
fetch(action_url)
    .then(response => response.json())
    .then(data => console.log(data));
</script>
```


### Methods

- `encryptedRouteJS(string $routeName, array $paramNames)`

  Generates a URL template with encrypted placeholders for JavaScript use.

  ```php
  $urlData = encryptedRouteJS('route.name', ['param1', 'param2']);
  // Returns: ['url' => '...', 'placeholders' => [...]]
  ```

