<?php
	/*
		Crispage - A lightweight CMS for developers
		core/plugins/PluginManager.php - Plugin manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/plugins/Plugin.php";

	class PluginManager {
		public function getPlugin(string $id = null) : ?Plugin {
			if ($id == null) return null;
			global $app;

			$plugin = $app->database->readRow("plugins", $id);
			if (!$plugin) return null;

			$plugin = new Plugin($plugin);

			return $plugin;
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

		public function deletePlugin(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("plugins", $id);
			$app->events->trigger("plugins.plugin_delete", $id);
		}

		public function existsPlugin(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("plugins", $id);
		}

		public function getPlugins(string $scope = null) {
			global $app;

			if ($scope) $dbplugs = $app->database->readRows("plugins", array("scope" => $scope));
			else $dbplugs = $app->database->readRows("plugins");

			$plugins = array();

			foreach ($dbplugs as $plugin)
				array_push($plugins, $this->getPlugin($plugin["id"]));

			return $plugins;
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
	}
?>
