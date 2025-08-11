## Installation

Install via Composer:

```bash
composer require paramguard/url-encoder
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="ParamGuard\UrlEncoder\UrlEncoderServiceProvider" --tag="config"
```

Then set these in your `.env`:
```env
URL_ENCODE_ENABLE=true
ENCRYPTION_SECRET_KEY=your-secret-key
ENCRYPTION_FIXED_IV=your-iv
ENCRYPTION_SALT=your-salt
```