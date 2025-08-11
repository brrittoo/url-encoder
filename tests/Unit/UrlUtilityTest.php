<?php

    namespace ParamGuard\UrlEncoder\Tests\Unit;

    use ParamGuard\UrlEncoder\Tests\TestCase;
    use ParamGuard\UrlEncoder\Utilities\Url;

    class UrlUtilityTest extends TestCase
    {
        public function test_safe_url_encoding()
        {
            $original = 'test/value#with?special=chars';
            $encoded = Url::safeUrl($original);
            $decoded = Url::safeUrl($encoded, true);

            $this->assertEquals($original, $decoded);
        }

        public function test_route_param_encoding()
        {
            $original = 'test-param';
            $encoded = Url::getRouteParamEncryptionDecryption($original, ENCRYPTED_PARAM);
            $decoded = Url::getRouteParamEncryptionDecryption($encoded, DECRYPTED_PARAM);

            $this->assertEquals($original, $decoded);
        }
    }
