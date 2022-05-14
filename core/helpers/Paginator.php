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
			return ceil(max(1, count($items)) / max(1, $nent));
		}
		
		public static function paginationQuery(array &$vars, int $dshow = 15, int $dpage = 1, string $vshow = "show", string $vpage = "page") : void {
			global $app;
			$vars["show"] = (is_numeric($app->request->query[$vshow])) ?  $app->request->query[$vshow] : $dshow;
			$vars["page"] = (is_numeric($app->request->query[$vpage])) ? $app->request->query[$vpage] : $dpage;
		}
		
		public static function paginateNum(array &$vars, array $data, string $vdata, string $vnpages = "npages", string $vshow = "show", string $vpage = "page") : void {
			$show = $vars[$vshow];
			$page = $vars[$vpage];
			$vars[$vnpages] = self::numPages($data, $show);
			$vars[$vdata] = self::paginate($data, $show, $page);
		}
	}
?>
