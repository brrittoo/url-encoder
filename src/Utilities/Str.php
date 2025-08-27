<?php

    namespace Brrittoo\UrlEncoder\Utilities;
    use Illuminate\Support\Str as CoreStr;
	class Str extends CoreStr
	{
        public static function isNull(mixed $value): bool
        {
            return is_null($value);
        }
		
		public static function isString($val)
		{
			return is_string($val);
		}
		public static function randomBytes($len)
		{
			return random_bytes($len);
		}
		
		public static function bin2hex($string)
		{
			return bin2hex($string);
		}
		
		public static function strlen($string)
		{
			return strlen($string);
		}
	}
