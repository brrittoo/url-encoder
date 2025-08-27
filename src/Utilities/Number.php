<?php
	
	namespace Brrittoo\UrlEncoder\Utilities;
	
	use Illuminate\Support\Number as CoreNumber;
	class Number extends CoreNumber
	{
		public static function isNumeric($val)
		{
			return is_numeric($val);
		}
	}