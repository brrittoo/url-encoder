<?php
	
	namespace Brritto\UrlEncoder\Utilities;
	
	use Illuminate\Support\Number as CoreNumber;
	class Number extends CoreNumber
	{
		public static function isNumeric($val)
		{
			return is_numeric($val);
		}
	}