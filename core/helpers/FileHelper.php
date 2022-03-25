<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/FileHelper.php - File helper

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php");

	class FileHelper {
		// https://stackoverflow.com/questions/2050859/copy-entire-contents-of-a-directory-to-another-using-php
		public static function copyRecurs(string $src, string $dest) {
			foreach (scandir($src) as $file) {
	 			if (!is_readable($src . "/" . $file)) continue;
				if (is_dir($src ."/" . $file) && ($file != ".") && ($file != "..") ) {
					@mkdir($dest . "/" . $file);
					self::copyRecurs($src . "/" . $file, $dest . "/" . $file);
				} else {
					@copy($src . "/" . $file, $dest . "/" . $file);
				}
			}
		}

		public static function deleteRecurs(string $file) {
			if (is_dir($file)) {
				array_map("FileHelper::deleteRecurs", glob($file . "/*"));
				rmdir($file);
			} else {
				@unlink($file);
			}
		}
	}
?>
