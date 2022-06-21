<?php
	/*
		Crispage - A lightweight CMS for developers
		core/frontend/FrontendApplication.php - Frontend Application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	namespace Crispage\Application;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once \Config::APPROOT . "/core/application/Application.php";

	class FrontendApplication extends Application {
		public function __construct() {
			parent::__construct();

			$tempname = $this->getSetting("template", "system");
			$this->template = new \Crispage\Render\Template(array("backend" => false, "template_name" => $tempname));
		}

		public function request(\Crispage\Routing\Request $request) : void {
			$this->events->trigger("app.request", $request);
			$this->request = $request;
			$this->events->trigger("app.languages.pre_load");
			$this->loadLanguages();
			$this->events->trigger("app.languages.post_load");
			$this->events->trigger("app.plugins.pre_load");
			$this->loadPlugins();
			$this->executePlugins();
			$this->events->trigger("app.plugins.post_load");
			$this->events->trigger("app.modules.pre_load");
			$this->page->loadModules();
			$this->events->trigger("app.modules.post_load");
			$this->events->trigger("page.pre_render");
			try {
				$app = $this;
				if (file_exists(\Config::APPROOT . "/views/{$request->route["view"]}.php"))
					include_once \Config::APPROOT . "/views/{$request->route["view"]}.php";
				else
					$this->error(new \Crispage\ApplicationException(500, "View nonexistent", "No view '{$request->route["view"]}' exists!"));
				$this->events->trigger("page.post_render");
			} catch (\Throwable $e) {
				$this->events->trigger("page.error_render", $e);
				throw new \Crispage\ApplicationException(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", null, $e);
			}
		}
	}
?>
