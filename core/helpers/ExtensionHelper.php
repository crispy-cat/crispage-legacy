<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/ExtensionHelper.php - Extension helper class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class ExtensionHelper {
		public static function loadClass(string $file) : ?string {
			if (!file_exists($file)) return null;
			include_once $file;
			@$name = array_pop(explode("/", explode(".", $file)[0]));
			if (!class_exists($name)) return null;
			return $name;
		}

		public static function getModuleInfo(string $class = null) : ?array {
			if ($class == null) return null;
			$infile = Config::APPROOT . "/modules/$class.json";
			if (!file_exists($infile)) return null;
			$df = fopen($infile, "r");
			@$fd = fread($df, filesize($infile));
			fclose($df);
			return ($fd !== false) ? json_decode($fd, true) : null;
		}

		public static function getPluginInfo(string $class = null) : ?array {
			if ($class == null) return null;
			$infile = Config::APPROOT . "/plugins/$class.json";
			if (!file_exists($infile)) return null;
			$df = fopen($infile, "r");
			@$fd = fread($df, filesize($infile));
			fclose($df);
			return ($fd !== false) ? json_decode($fd, true) : null;
		}

		public static function getAvailableModules(string $scope = null) : array {
			global $app;
			$classes = array();
			if ($scope)
				$modules = $app->database->readRows("installation", array("type" => "module", "scope" => $scope));
			else
				$modules = $app->database->readRows("installation", array("type" => "module"));
			foreach ($modules as $class)
				array_push($classes, self::getModuleInfo($class["class"]));
			return $classes;
		}

		public static function getInstalledPlugins(string $scope = null) : array {
			global $app;
			$classes = array();
			if ($scope)
				$plugins = $app->database->readRows("installation", array("type" => "plugin", "scope" => $scope));
			else
				$plugins = $app->database->readRows("installation", array("type" => "plugin"));
			foreach ($plugins as $class)
				array_push($classes, self::getPluginInfo($class["class"]));
			return $classes;
		}


		public static function getExtensionPackInfo(string $dir) : array {
			$infile = $dir . "/modules/package.json";
			if (!file_exists($infile)) throw new Exception("Extension pack at $dir does not have a package.json file");;
			$df = fopen($infile, "r");
			@$fd = fread($df, filesize($infile));
			fclose($df);
			if ($fd !== false) throw new Exception("Extension pack at $dir is corrupt");
			return json_decode($fd, true);
		}

		public static function registerExtension(string $class, string $type, string $scope = "frontend") : string {
			global $app;
			$rows = $app->database->readRows("installation");
			$last = end($rows);
			$id = ($last) ? ($last["id"] + 1) : 0;
			$app->database->writeRow("installation", $id, array("class" => $class, "type" => $type, "scope" => $scope));
			return $id;
		}

		public static function unregisterExtension(string $class, string $type, string $scope) : void {
			global $app;
			$rows = $app->database->readRows("installation", array("class" => $class, "type" => $type, "scope" => $scope));
			foreach ($rows as $row) $app->database->deleteRow($row->id);
		}

		public static function unregisterExtensionByID(string $id) : void {
			global $app;
			$app->database->deleteRow("installation", $id);
		}

		public static function getLocation(string $class, string $type, string $scope = "frontend") : ?string {
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
