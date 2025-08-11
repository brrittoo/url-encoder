<?php

	namespace Utilities;
    use Illuminate\Support\Arr as CoreArr;
	class Arr extends CoreArr
	{
        public static function isArr(mixed $value): bool
        {
            return is_array($value);
        }

        public static function isOfType($array):bool
        {
            return is_array($array);
        }
	}
