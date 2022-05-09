<?php
	/*
		Crispage - A lightweight CMS for developers
		core/backend/BackendApplication.php - Backend application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/Application.php";
	require_once Config::APPROOT . "/core/backend/BackendMenuItem.php";

	class BackendApplication extends Application {
		private array $menuItems = array();

		public function __construct() {
			parent::__construct();

			$tempname = $this->getSetting("backend_template", "system");
			$this->template = new Template(array("backend" => true, "template_name" => $tempname));
		}

		public function request(Request $request) : void {
			$this->events->trigger("app.backend.request", $request);
			$this->request = $request;
			$this->events->trigger("app.languages.pre_load");
			$this->loadLanguages();
			$this->events->trigger("app.languages.post_load");
			$this->events->trigger("app.plugins.pre_load");
			$this->loadPlugins();
			$this->executePlugins();
			$this->events->trigger("app.plugins.post_load");
			try {
				$app = $this;
				if (file_exists(Config::APPROOT . "/backend/views/$request->slug.php"))
					include_once Config::APPROOT . "/backend/views/$request->slug.php";
				else
					throw new Exception("No view '$request->slug' exists!");
			} catch (Throwable $e) {
				throw new ApplicationException(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", null, $e);
			}
		}

		public function loadPlugins(string $scope = "backend") : void {
			parent::loadPlugins($scope);
		}

		public function getBackendMenuItems() : array {
			$dbitems = $this->database->readRows("backend_menu");

			$items = array();
			foreach ($dbitems as $item)
				array_push($items, new BackendMenuItem($item));
			return array(...$items, ...$this->menuItems);
		}

		public function addBackendMenuItem(BackendMenuItem $item) {
			$this->menuItems[] = $item;
		}
	}
?>