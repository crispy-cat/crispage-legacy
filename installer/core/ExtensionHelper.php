<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/core/ExtensionHelper.php - Extension helper

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class ExtensionHelper {
		public function getExtensionPackInfo(string $dir) : array {
			$infile = $dir . "/modules/package.json";
			if (!file_exists($infile)) throw new Exception("Extension pack at $dir does not have a package.json file");;
			$df = fopen($infile, "r");
			@$fd = fread($df, filesize($infile));
			fclose($df);
			if ($fd !== false) throw new Exception("Extension pack at $dir is corrupt");
			return json_decode($fd, true);
		}
	}
?>
