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
			$this->page->loadModules();
			try {
				$app = $this;
				if (file_exists(Config::APPROOT . "/views/{$request->route["view"]}.php"))
					include_once Config::APPROOT . "/views/{$request->route["view"]}.php";
				else
					throw new Exception("No view '{$request->route["view"]}' exists!");
			} catch (Throwable $e) {
				$this->error(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", $e);
			}
		}
	}
?>
