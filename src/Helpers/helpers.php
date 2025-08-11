<?php
	
	const ENCRYPTED_PARAM = 'encrypt';
	const DECRYPTED_PARAM = 'decrypt';
	
	if (!function_exists('enableUrlEncode')) {
		/**
		 * Check if URL parameter encoding is enabled in config.
		 *
		 * @return bool True if enabled, false otherwise.
		 */
		function enableUrlEncode(): bool
		{
			return (bool) config('url-encoder.is_encoding_enable');
		}
	}
	
	if (!function_exists('encryptedRoute')) {
		/**
		 * Generate a route URL with encrypted parameters.
		 * Supports string, numeric, or array of parameters.
		 *
		 * Example usage:
		 *    encrypted_route('user.show', ['id' => 123])
		 *    encrypted_route('user.show', 123)
		 *
		 * @param string $name Route name
		 * @param array|string|int $parameters Route parameters, can be a single value or key=>value array.
		 * @param bool $absolute Whether to generate absolute URL (default true)
		 * @return string URL with encrypted parameters if enabled.
		 */
		function encryptedRoute(string $name, $parameters = [], bool $absolute = true): string
		{
			if (!enableUrlEncode()) {
				return route($name, $parameters, $absolute);
			}
			
			// Single parameter (string or number)
			if (ParamGuard\UrlEncoder\Utilities\Str::isString($parameters) || ParamGuard\UrlEncoder\Utilities\Number::isNumeric
				($parameters)) {
				$encrypted = \ParamGuard\UrlEncoder\Utilities\Url::getRouteParamEncryptionDecryption($parameters, ENCRYPTED_PARAM);
				return route($name, $encrypted, $absolute);
			}
			
			// Array of parameters
			$encryptedParams = [];
			foreach ((array) $parameters as $key => $value) {
				$encryptedParams[$key] = \ParamGuard\UrlEncoder\Utilities\Url::getRouteParamEncryptionDecryption($value, ENCRYPTED_PARAM);
			}
			
			return route($name, $encryptedParams, $absolute);
		}
	}
	
	
	if (!function_exists('encryptedRouteJS')) {
		/**
		 * Generate a route URL string for JavaScript usage,
		 * returning the URL with encrypted placeholders for parameters.
		 *
		 * The placeholders can then be replaced dynamically in JS.
		 *
		 * Example:
		 *    $data = encryptedRouteJS('project.task.show', ['project_id', 'task_id']);
		 *    // $data['url'] = "http://.../project/__PROJECT_ID__/task/__TASK_ID__" (encrypted placeholders)
		 *    // $data['placeholders'] = ['project_id' => '__ENCODED_PROJECT_ID__', 'task_id' => '__ENCODED_TASK_ID__']
		 *
		 * @param string $name Route name
		 * @param array $paramKeys List of route parameter keys
		 * @param bool $absolute Whether to generate absolute URL (default true)
		 * @return array ['url' => string, 'placeholders' => array]
		 */
		function encryptedRouteJS(string $name, array $paramKeys, bool $absolute = true): array
		{
			$placeholders = [];
			$params = [];
			
			foreach ($paramKeys as $key) {
				// Create a placeholder string for the param key, e.g. __ID__
				$placeholder = "__" . strtoupper($key) . "__";
				
				// Encrypt the placeholder if encoding is enabled
				$encoded = enableUrlEncode()
					? \ParamGuard\UrlEncoder\Utilities\Url::getRouteParamEncryptionDecryption($placeholder, ENCRYPTED_PARAM)
					: $placeholder;
				
				$placeholders[$key] = $encoded;
				$params[$key] = $encoded;
			}
			
			return [
				'url' => route($name, $params, $absolute),
				'placeholders' => $placeholders,
			];
		}
	}
	
	