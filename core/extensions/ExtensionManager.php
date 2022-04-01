<?php
	/*
		Crispage - A lightweight CMS for developers
		core/extensions/ExtensionManager.php - Extension manager

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.7.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	
	require_once Config::APPROOT . "/core/extensions/Module.php";
	require_once Config::APPROOT . "/core/extensions/Plugin.php";

	class ExtensionManager {
		public function getModule(string $id = null) : ?Module {
			if ($id == null) return null;
			global $app;

			$module = $app->database->readRow("modules", $id);
			if (!$module) return null;

			$module = new Module($module);

			return $module;
		}
		
		public function getPlugin(string $id = null) : ?Plugin {
			if ($id == null) return null;
			global $app;

			$plugin = $app->database->readRow("plugins", $id);
			if (!$plugin) return null;

			$plugin = new Plugin($plugin);

			return $plugin;
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
		
		public function setPlugin(string $id, Plugin $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("plugins", $id, array(
				"class"	=> $data->class,
				"priority"	=> $data->priority,
				"scope" => $data->scope,
				"created" => $data->created,
				"modified" => $data->modified,
				"options" => $data->options
			));
			$app->events->trigger("plugins.plugin_set", $id);
		}
		
		public function deleteModule(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("modules", $id);
			$app->events->trigger("modules.module_delete", $id);
		}
		
		public function deletePlugin(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("plugins", $id);
			$app->events->trigger("plugins.plugin_delete", $id);
		}
		
		public function existsModule(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("modules", $id);
		}
		
		public function existsPlugin(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("plugins", $id);
		}
		
		public function gModules(string $scope = null) : Generator {
			global $app;
			
			if ($scope) $dbmodules = $app->database->readRows("modules", array("scope" => $scope));
			else $dbmodules = $app->database->readRows("modules");


			foreach ($dbmodules as $module)
				yield new Module($module);
		}
		
		public function gPlugins(string $scope = null) : Generator {
			global $app;

			if ($scope) $dbplugs = $app->database->readRows("plugins", array("scope" => $scope));
			else $dbplugs = $app->database->readRows("plugins");
			
			foreach ($dbplugs as $plugin)
				yield new Plugin($plugin);
		}
		
		public function getModules(string $scope = null) {
			$modules = array();

			foreach ($this->gModules($scope) as $module)
				$modules[] = $module;;

			return $modules;
		}
		
		public function getPlugins(string $scope = null) {
			$plugins = array();

			foreach ($this->gPlugins($scope) as $plugin)
				$plugins[] = $plugin;

			return $plugins;
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
		
		public function getPluginInfo(string $class = null) : ?array {
			if ($class == null) return null;
			$infile = Config::APPROOT . "/plugins/$class.json";
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
		
		public function getInstalledPlugins(string $scope = null) : array {
			global $app;
			$classes = array();
			if ($scope)
				$plugins = $app->database->readRows("installation", array("type" => "plugin", "scope" => $scope));
			else
				$plugins = $app->database->readRows("installation", array("type" => "plugin"));
			foreach ($plugins as $class)
				array_push($classes, $this->getPluginInfo($class["class"]));
			return $classes;
		}
		
		
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
