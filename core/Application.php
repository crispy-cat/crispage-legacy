<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Application.php - Frontend Application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/ApplicationBase.php";

	class Application extends ApplicationBase {
		public function __construct() {
			parent::__construct();

			$tempname = $this->getSetting("template", "system");
			$this->template = new Template(array("backend" => false, "template_name" => $tempname));
		}

		public function request(Request $request) {
			$this->events->trigger("app.request", $request);
			$this->request = $request;
			$this->events->trigger("app.plugins.pre_load");
			$this->loadPlugins();
			$this->events->trigger("app.plugins.post_load");
			$this->events->trigger("app.modules.pre_load");
			$this->page->loadModules();
			$this->events->trigger("app.modules.post_load");
			try {
				$app = $this;
				if (file_exists(Config::APPROOT . "/views/{$request->route["view"]}.php"))
					include_once Config::APPROOT . "/views/{$request->route["view"]}.php";
				else
					throw new Exception("No view '{$request->route["view"]}' exists!");
			} catch (Throwable $e) {
				throw new ApplicationException(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", null, $e);
			}
		}
	}
?>
