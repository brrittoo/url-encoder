<?php
	namespace ParamGuard\UrlEncoder\Utilities;
	
	class EncodeClipper {
		public static function customEncryptionDecryption($value, $action, $isURL = false) {
			$secret_key = config('url-encoder.url_encryption_secret_key');
			if ($action == ENCRYPTED_PARAM) {
				$value = is_array($value) ? implode('|', $value) : $value;
				$encrypted = self::encrypt_ctr($value, $secret_key);
				return $isURL ? self::base64UrlEncode($encrypted) : $encrypted;
			}
			
			if ($action == DECRYPTED_PARAM) {
				$value = $isURL ? self::base64UrlDecode($value) : $value;
				return self::decrypt_ctr($value, $secret_key);
			}
			
			return $value;
		}
		
		private static function encrypt_ctr($data, $key) {
			$iv = random_bytes(16); // CTR requires 16-byte IV
			$key = hash('sha256', $key, true); // Raw binary key
			$encrypted = openssl_encrypt($data, 'AES-128-CTR', $key, OPENSSL_RAW_DATA, $iv);
			return $iv.$encrypted; // Combine IV + ciphertext
		}
		
		private static function decrypt_ctr($data, $key) {
			$iv = substr($data, 0, 16);
			$ciphertext = substr($data, 16);
			$key = hash('sha256', $key, true); // Raw binary key
			return openssl_decrypt($ciphertext, 'AES-128-CTR', $key, OPENSSL_RAW_DATA, $iv);
		}
		
		private static function base64UrlEncode($data) {
			return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
		}
		
		private static function base64UrlDecode($data) {
			return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4));
		}
	}