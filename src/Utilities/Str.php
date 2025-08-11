<?php

	namespace Utilities;
    use Illuminate\Support\Str as CoreStr;
	class Str extends CoreStr
	{
        public static function isNull(mixed $value): bool
        {
            return is_null($value);
        }
	}
