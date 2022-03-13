<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/core/Installer.php - Installer application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/ApplicationBase.php";
	require_once Config::APPROOT . "/installer/core/ExtensionHelper.php";

	class Installer extends ApplicationBase {
		public ExtensionHelper $extensions;

		public function __construct() {
			parent::__construct();

			$this->template = new Template(array("backend" => true, "template_name" => "installer"));
			$this->extensions = new ExtensionHelper();
		}

		public function request(Request $request) {
			$this->events->trigger("app.installer.request", $request);
			$this->request = $request;
			try {
				$app = $this;
				if (file_exists(Config::APPROOT . "/installer/views/$request->slug.php"))
					include_once Config::APPROOT . "/installer/views/$request->slug.php";
				else
					throw new Exception("No view '$request->slug' exists!");
			} catch (Throwable $e) {
				$this->error(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", $e);
			}
		}
	}
?>
