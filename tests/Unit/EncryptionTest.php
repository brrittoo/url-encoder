<?php

    namespace ParamGuard\UrlEncoder\Tests\Unit;

    use ParamGuard\UrlEncoder\Tests\TestCase;
    use ParamGuard\UrlEncoder\Utilities\EncodeClipper;

    class EncryptionTest extends TestCase
    {
        public function test_encryption_decryption_cycle()
        {
            $original = 'test-value';
            $encrypted = EncodeClipper::customEncryptionDecryption($original, ENCRYPTED_PARAM);
            $decrypted = EncodeClipper::customEncryptionDecryption($encrypted, DECRYPTED_PARAM);

            $this->assertEquals($original, $decrypted);
        }

        public function test_array_encryption()
        {
            $original = ['param1' => 'value1', 'param2' => 'value2'];
            $encrypted = EncodeClipper::customEncryptionDecryption($original, ENCRYPTED_PARAM);
            $decrypted = EncodeClipper::customEncryptionDecryption($encrypted, DECRYPTED_PARAM);

            $this->assertEquals(implode('|', $original), $decrypted);
        }

        public function test_url_safe_encryption()
        {
            $original = 'test/value#with?special=chars';
            $encrypted = EncodeClipper::customEncryptionDecryption($original, ENCRYPTED_PARAM, true);
            $decrypted = EncodeClipper::customEncryptionDecryption($encrypted, DECRYPTED_PARAM, true);

            $this->assertEquals($original, $decrypted);
        }
    }
