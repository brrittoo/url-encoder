<?php

    namespace ParamGuard\UrlEncoder\Tests\Feature;

    use ParamGuard\UrlEncoder\Tests\TestCase;
    use Illuminate\Support\Facades\Route;

    class UrlGenerationTest extends TestCase
    {
        public function test_route_generation_with_encryption()
        {
            Route::get('/test/{id}', function () {})->name('test.route');

            $original = 'test-value';
            $url = app('url')->route('test.route', ['id' => $original]);

            // Verify the ID in the URL is encrypted
            $this->assertStringNotContainsString($original, $url);
            $this->assertMatchesRegularExpression('/\/test\/[a-zA-Z0-9_\-]+/', $url);
        }

        public function test_route_generation_without_encryption()
        {
            // Disable encryption for this test
            config(['url-encoder.is_encoding_enable' => false]);

            Route::get('/test/{id}', function () {})->name('test.route');

            $original = 'test-value';
            $url = app('url')->route('test.route', ['id' => $original]);

            // Verify the ID in the URL is not encrypted
            $this->assertStringContainsString($original, $url);
        }
    }
