<?php
	/*
		Crispage - A lightweight CMS for developers
		core/installer/InstallerApplication.php - Installer application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/Application.php";

	class InstallerApplication extends Application {
		public function __construct() {
			$this->page = new Page();
			$this->events = new EventManager();
			$this->auth = new Authenticator();

			$this->assets = new ApplicationAssetManagers();
			$this->assets->addAssetManager("articles",		new AssetManager("articles", "Article"));
			$this->assets->addAssetManager("categories",	new AssetManager("categories", "Category"));
			$this->assets->addAssetManager("comments",		new AssetManager("comments", "Comment"));
			$this->assets->addAssetManager("menus",			new AssetManager("menus", "Menu"));
			$this->assets->addAssetManager("menu_items",	new AssetManager("menuitems", "MenuItem"));
			$this->assets->addAssetManager("modules",		new AssetManager("modules", "Module"));
			$this->assets->addAssetManager("plugins",		new AssetManager("plugins", "Plugin"));
			$this->assets->addAssetManager("users",			new AssetManager("users", "User"));
			$this->assets->addAssetManager("usergroups",	new AssetManager("usergroups", "UserGroup"));
			$this->assets->addAssetManager("bans",			new AssetManager("bans", "Ban"));
			$this->assets->addAssetManager("sessions",		new AssetManager("sessions", "Session"));

			$this->template = new Template(array("backend" => true, "template_name" => "installer"));
			require_once Config::APPROOT . "/core/events/defaultevents.php";
		}

		public function initDatabase(string $type, array $options) : void {
			$this->database = new JSONDatabase($options["location"], $options["name"], $options["pretty"]);
		}

		public function request(Request $request)  : void {
			$this->events->trigger("app.installer.request", $request);
			$this->request = $request;
			$this->events->trigger("app.languages.pre_load");
			$this->loadLanguages();
			$this->events->trigger("app.languages.post_load");
			try {
				$app = $this;
				if (file_exists(Config::APPROOT . "/installer/views/$request->slug.php"))
					include_once Config::APPROOT . "/installer/views/$request->slug.php";
				else
					throw new Exception("No view '$request->slug' exists!");
			} catch (Throwable $e) {
				throw new ApplicationException(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", null, $e, false);
			}
		}

		public function error(Throwable $e) : void {
			if ($e instanceof ApplicationException)
				parent::error(new ApplicationException($e->getHttpStatus(), $e->getPageTitle(), $e->getMessage(), null, $e, false));
			else
				parent::error(new ApplicationException(500, "Internal Server Error", $e->getMessage(), null, $e, false));
		}
	}
?>
