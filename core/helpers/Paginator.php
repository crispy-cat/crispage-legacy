<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/Paginator.php - Pagination helper

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Paginator {
		public static function paginate(array $in, int $nent, int $page) : array {
			$out = array();
			if ($nent < 1) $nent = 1;
			if ($page < 1) $page = 1;
			$sent = $nent * ($page - 1);
			$eent = $nent * $page - 1;
			for ($i = $sent; $i <= $eent; $i++) {
				if (isset($in[$i])) array_push($out, $in[$i]);
				else break;
			}
			return $out;
		}

		public static function numPages(array $items, int $nent) : int {
			if ($nent == 0) return 1;
			return ceil(count($items) / $nent);
		}

		public static function sPaginate(array $in, $nent, $page) : array {
			if (!is_numeric($nent)) return $in;
			if (!is_numeric($page)) $page = 1;
			return self::paginate($in, $nent, $page);
		}
	}
?>
