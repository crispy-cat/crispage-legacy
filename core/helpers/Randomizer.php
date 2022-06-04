<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/Randomizer.php - Random helper

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Helpers;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Randomizer {
		public const RCHARS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./";

		public static function randomString(int $length = 0, int $base = 0) : string {
			if ($base < 1) $base = 64;
			if ($length < 1) $length = 64;
			$str = "";
			for ($i = 0; $i < $length; $i++)
				$str .= self::RCHARS[random_int(0, $base - 1)];
			return $str;
		}
	}
?>
