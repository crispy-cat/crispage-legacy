<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/core/ExtensionManager.php - Extension manager

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class ExtensionManager {
		public function getExtensionPackInfo(string $dir) : array {
			$infile = $dir . "/modules/package.json";
			if (!file_exists($infile)) throw new Exception("Extension pack at $dir does not have a package.json file");;
			$df = fopen($infile, "r");
			@$fd = fread($df, filesize($infile));
			fclose($df);
			if ($fd !== false) throw new Exception("Extension pack at $dir is corrupt");
			return json_decode($fd, true);
		}

		public function registerExtension(string $class, string $type, string $scope = "frontend") {
			global $app;
			$rows = $app->database->readRows("installation");
			$last = end($rows);
			$id = ($last) ? ($last["id"] + 1) : 0;
			$app->database->writeRow("installation", $id, array("class" => $class, "type" => $type, "scope" => $scope));
			return $id;
		}

		public function unregisterExtension(string $class, string $type, string $scope) {
			global $app;
			$rows = $app->database->readRows("installation", array("class" => $class, "type" => $type, "scope" => $scope));
			foreach ($rows as $row) $app->database->deleteRow($row->id);
		}

		public function unregisterExtensionByID(string $id) {
			global $app;
			$app->database->deleteRow("installation", $id);
		}

		public function getLocation(string $class, string $type, string $scope = "frontend") : ?string {
			switch ($type) {
				case "view":
					return Config::APPROOT . (($scope == "backend") ? "/backend" : "/") . "views/" . $class;
				case "template":
					return Config::APPROOT . "/templates/" . $class;
				case "module":
					return Config::APPROOT . "/modules/" . $class;
				case "plugin":
					return Config::APPROOT . "/plugins/" . $class;
				default:
					return null;
			}
		}
	}
?>
