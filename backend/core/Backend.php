<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/core/Backend.php - Backend application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/ApplicationBase.php";

	class Backend extends ApplicationBase {
		public function __construct() {
			parent::__construct();

			$tempname = $this->getSetting("backend_template", "system");
			$this->template = new Template(array("backend" => true, "template_name" => $tempname));
		}

		public function request(Request $request) {
			$this->events->trigger("app.backend.request", $request);
			$this->request = $request;
			$this->events->trigger("app.plugins.pre_load");
			$this->loadPlugins();
			$this->events->trigger("app.plugins.post_load");
			try {
				$app = $this;
				if (file_exists(Config::APPROOT . "/backend/views/$request->slug.php"))
					include_once Config::APPROOT . "/backend/views/$request->slug.php";
				else
					throw new Exception("No view '$request->slug' exists!");
			} catch (Throwable $e) {
				$this->error(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", $e);
			}
		}
	}
?>
