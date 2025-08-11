<?php

    namespace ParamGuard\UrlEncoder\Utilities;
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
	}
