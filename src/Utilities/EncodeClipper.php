<?php
	namespace Brrittoo\UrlEncoder\Utilities;
	
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
			$iv = random_bytes(16);
			$key = hash('sha256', $key, true);
			$encrypted = openssl_encrypt($data, 'AES-128-CTR', $key, OPENSSL_RAW_DATA, $iv);
			return $iv.$encrypted;
		}
		
		private static function decrypt_ctr($data, $key) {
			$iv = substr($data, 0, 16);
			$ciphertext = substr($data, 16);
			$key = hash('sha256', $key, true);
			return openssl_decrypt($ciphertext, 'AES-128-CTR', $key, OPENSSL_RAW_DATA, $iv);
		}
		
		
		private static $customReplacements = [
			'/' => '__G__',
			'#' => '__A__',
			'?' => '__L__',
			'&' => '__I__',
			'=' => '__B__',
			'%' => '__T__',
			'+' => '__X__',
			'-' => '__N__',
			'_' => '__U__',
		];

		
		private static function base64UrlEncode($data) {
			$encoded = base64_encode($data);
			$encoded = strtr($encoded, self::$customReplacements);
			return rtrim($encoded, '='); // Remove any remaining padding
		}
		
		private static function base64UrlDecode($data) {
			// Add padding if needed (base64 needs length divisible by 4)
			$padding = strlen($data) % 4;
			if ($padding) {
				$data .= str_repeat('=', 4 - $padding);
			}
			
			$reverseMap = array_flip(self::$customReplacements);
			$reversed = strtr($data, $reverseMap);
			return base64_decode($reversed);
		}
		

	}