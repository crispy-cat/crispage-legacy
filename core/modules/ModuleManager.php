<?php
	/*
		Crispage - A lightweight CMS for developers
		core/modules/ModuleManager.php - Module manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/modules/Module.php";

	class ModuleManager {
		public function getModule(string $id = null) : ?Module {
			if ($id == null) return null;
			global $app;

			$module = $app->database->readRow("modules", $id);
			if (!$module) return null;

			$module = new Module($module);

			return $module;
		}

		public function setModule(string $id, Module $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("modules", $id, array(
				"title"	=> $data->title,
				"class"	=> $data->class,
				"pos"	=> $data->pos,
				"ord"	=> $data->ord,
				"created" => $data->created,
				"modified" => $data->modified,
				"options" => $data->options
			));
			$app->events->trigger("modules.module_set", $id);
		}

		public function deleteModule(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("modules", $id);
			$app->events->trigger("modules.module_delete", $id);
		}

		public function existsModule(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("modules", $id);
		}

		public function getModules(string $scope = null) {
			global $app;

			if ($scope) $dbmodules = $app->database->readRows("modules", array("scope" => $scope));
			else $dbmodules = $app->database->readRows("modules");

			$modules = array();

			foreach ($dbmodules as $module)
				array_push($modules, $this->getModule($module["id"]));

			return $modules;
		}

		public function getModuleInfo(string $class = null) : ?array {
			if ($class == null) return null;
			$infile = Config::APPROOT . "/modules/$class.json";
			if (!file_exists($infile)) return null;
			$df = fopen($infile, "r");
			@$fd = fread($df, filesize($infile));
			fclose($df);
			return ($fd !== false) ? json_decode($fd, true) : null;
		}

		public function getAvailableModules(string $scope = null) : array {
			global $app;
			$classes = array();
			if ($scope)
				$modules = $app->database->readRows("installation", array("type" => "module", "scope" => $scope));
			else
				$modules = $app->database->readRows("installation", array("type" => "module"));
			foreach ($modules as $class)
				array_push($classes, $this->getModuleInfo($class["class"]));
			return $classes;
		}
	}
?>
