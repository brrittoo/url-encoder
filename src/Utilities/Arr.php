<?php

	namespace Brrittoo\UrlEncoder\Utilities;
    use Illuminate\Support\Arr as CoreArr;
	class Arr extends CoreArr
	{
		
		/**
		 * Check if a value exists in an array (strict comparison by default).
		 *
		 * @param mixed $needle
		 * @param array $haystack
		 * @param bool  $strict
		 * @return bool
		 */
		public static function inArray($needle, array $haystack, bool $strict = false): bool
		{
			return in_array($needle, $haystack, $strict);
		}
		
	}
