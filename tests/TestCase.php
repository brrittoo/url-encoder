<?php

    namespace ParamGuard\UrlEncoder\Tests;

    use Orchestra\Testbench\TestCase as OrchestraTestCase;
    use ParamGuard\UrlEncoder\UrlEncoderServiceProvider;

    class TestCase extends OrchestraTestCase
    {
        protected function getPackageProviders($app)
        {
            return [
                UrlEncoderServiceProvider::class
            ];
        }

        protected function getEnvironmentSetUp($app)
        {
            $app['config']->set('url-encoder', [
                'is_encoding_enable' => true,
                'encryption_method' => 'AES-256-CBC',
                'encryption_secret_key' => 'test-secret-key',
                'encryption_fixed_iv' => 'test-iv',
                'encryption_salt' => 'test-salt'
            ]);
        }
    }
