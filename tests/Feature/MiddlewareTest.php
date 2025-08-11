<?php

    namespace ParamGuard\UrlEncoder\Tests\Feature;

    use ParamGuard\UrlEncoder\Tests\TestCase;
    use Illuminate\Support\Facades\Route;

    class MiddlewareTest extends TestCase
    {
        public function test_middleware_decryption()
        {
            Route::middleware('web')->get('/test/{id}', function ($id) {
                return $id;
            })->name('test.route');

            // First encrypt a value
            $original = 'test-value';
            $encrypted = app('url')->route('test.route', ['id' => $original]);
            $encryptedId = substr($encrypted, strrpos($encrypted, '/') + 1);

            // Test the middleware decrypts it
            $response = $this->get("/test/{$encryptedId}");
            $response->assertSee($original);
        }
    }
