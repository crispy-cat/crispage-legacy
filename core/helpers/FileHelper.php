<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/FileHelper.php - File helper

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	namespace Crispage\Helpers;

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
				array_map("\Crispage\Helpers\FileHelper::deleteRecurs", glob($file . "/*"));
				rmdir($file);
			} else {
				@unlink($file);
			}
		}

		public static function uncompress(string $file, string $dest) : bool {
			if (!mkdir($dest)) return false;
			if (pathinfo($file, PATHINFO_EXTENSION) == "zip") {
				$fzip = $file;
			} else {
				$phar = new \PharData($file, \RecursiveDirectoryIterator::SKIP_DOTS);
				$phar->convertToData(\Phar::ZIP);
				$fzip = preg_replace("/(?:\.tar)?\.[^\.]+$/", ".zip", $file);
			}
			$zip = new \ZipArchive();
			if ($zip->open($fzip) === true) {
				$zip->extractTo($dest);
				$zip->close();
				return true;
			} else {
				return false;
			}
		}
	}
?>
