<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Application.php - Application base class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	namespace Crispage\Application;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once \Config::APPROOT . "/core/ApplicationException.php";
	require_once \Config::APPROOT . "/core/events/EventManager.php";
	require_once \Config::APPROOT . "/core/assets/ApplicationAssetManagers.php";

	require_once \Config::APPROOT . "/core/routing/Router.php";
	require_once \Config::APPROOT . "/core/routing/Request.php";
	require_once \Config::APPROOT . "/core/render/Page.php";
	require_once \Config::APPROOT . "/core/render/Template.php";

	require_once \Config::APPROOT . "/core/database/Database.php";
	require_once \Config::APPROOT . "/core/database/JSONDatabase.php";
	require_once \Config::APPROOT . "/core/database/PDODatabase.php";
	require_once \Config::APPROOT . "/core/database/SQLiteDatabase.php";
	require_once \Config::APPROOT . "/core/database/MySQLDatabase.php";

	require_once \Config::APPROOT . "/core/helpers/Paginator.php";
	require_once \Config::APPROOT . "/core/helpers/RenderHelper.php";
	require_once \Config::APPROOT . "/core/helpers/Randomizer.php";
	require_once \Config::APPROOT . "/core/helpers/Mailer.php";
	require_once \Config::APPROOT . "/core/helpers/FileHelper.php";
	require_once \Config::APPROOT . "/core/helpers/ExtensionHelper.php";
	require_once \Config::APPROOT . "/core/helpers/FormHelper.php";

	require_once \Config::APPROOT . "/core/users/UserPermissions.php";
	require_once \Config::APPROOT . "/core/users/Authenticator.php";
	require_once \Config::APPROOT . "/core/i18n/I18n.php";

	require_once \Config::APPROOT . "/core/assets/classes/Article.php";
	require_once \Config::APPROOT . "/core/assets/classes/Category.php";
	require_once \Config::APPROOT . "/core/assets/classes/Comment.php";
	require_once \Config::APPROOT . "/core/assets/classes/Menu.php";
	require_once \Config::APPROOT . "/core/assets/classes/MenuItem.php";
	require_once \Config::APPROOT . "/core/assets/classes/Module.php";
	require_once \Config::APPROOT . "/core/assets/classes/Plugin.php";
	require_once \Config::APPROOT . "/core/assets/classes/User.php";
	require_once \Config::APPROOT . "/core/assets/classes/UserGroup.php";
	require_once \Config::APPROOT . "/core/assets/classes/Session.php";
	require_once \Config::APPROOT . "/core/assets/classes/Ban.php";

	abstract class Application {
		public \Crispage\Routing\Request $request;
		public \Crispage\Render\Page $page;
		public \Crispage\Render\Template $template;

		public ?\Crispage\Database\Database $database = null;
		public \Crispage\Events\EventManager $events;

		public \Crispage\Assets\ApplicationAssetManagers $assets;

		public \Crispage\Users\Authenticator $auth;
		public \Crispage\I18n\I18n $i18n;

		public array $loadedPlugins	= array();
		public bool $pluginsExecd	= false;
		public bool $hasError		= false;
		public array $vars			= array();

		public function __construct() {

			$this->page = new \Crispage\Render\Page();
			$this->events = new \Crispage\Events\EventManager();
			$this->auth = new \Crispage\Users\Authenticator();
			$this->i18n = new \Crispage\I18n\I18n();

			$this->assets = new \Crispage\Assets\ApplicationAssetManagers();
			$this->assets->addAssetManager("articles",		new \Crispage\Assets\AssetManager("articles", "\Crispage\Assets\Article"));
			$this->assets->addAssetManager("categories",	new \Crispage\Assets\AssetManager("categories", "\Crispage\Assets\Category"));
			$this->assets->addAssetManager("comments",		new \Crispage\Assets\AssetManager("comments", "\Crispage\Assets\Comment"));
			$this->assets->addAssetManager("menus",			new \Crispage\Assets\AssetManager("menus", "\Crispage\Assets\Menu"));
			$this->assets->addAssetManager("menu_items",	new \Crispage\Assets\AssetManager("menuitems", "\Crispage\Assets\MenuItem"));
			$this->assets->addAssetManager("modules",		new \Crispage\Assets\AssetManager("modules", "\Crispage\Assets\Module"));
			$this->assets->addAssetManager("plugins",		new \Crispage\Assets\AssetManager("plugins", "\Crispage\Assets\Plugin"));
			$this->assets->addAssetManager("users",			new \Crispage\Assets\AssetManager("users", "\Crispage\Assets\User"));
			$this->assets->addAssetManager("usergroups",	new \Crispage\Assets\AssetManager("usergroups", "\Crispage\Assets\UserGroup"));
			$this->assets->addAssetManager("bans",			new \Crispage\Assets\AssetManager("bans", "\Crispage\Assets\Ban"));
			$this->assets->addAssetManager("sessions",		new \Crispage\Assets\AssetManager("sessions", "\Crispage\Assets\Session"));

			if (!($this instanceof \Crispage\Application\InstallerApplication)) $this->initDatabase(\Config::DB_TYPE, \Config::DB_LOC, \Config::DB_NAME, \Config::DB_OPTIONS);
		}

		public function &__invoke(string $name) {
			if (isset($this->$name)) return $this->$name;
			$am = ($this->assets)($name);
			if ($am) return $am;
			return $this->vars[$name] ?? null;
		}

		public function __destruct() {
			if (isset($this->database)) $this->database->writeChanges();
		}

		public function start(string $basepath = "", string $default = "index") : void {
			// Set language
			$this("i18n")->setLanguage($this->getSetting("language", "en-US"));

			// Set generator
			$this->page->metas["generator"] = array("name" => "generator", "content" => "Crispage " . CRISPAGE);

			// Register events
			$this->events->registerAction(new \Crispage\Events\EventAction(array(
				"id" => "crispage.articlesetroute",
				"event" => "assets.articles.set",
				"priority" => -64,
				"action" => function($app, $id) {
					$app->database->writeRow("routes", \Crispage\Routing\Router::getArticleRoute($id), array("item_id" => $id, "view" => "core/article"));
				}
			)));

			$this->events->registerAction(new \Crispage\Events\EventAction(array(
				"id" => "crispage.articledeleteroute",
				"event" => "assets.articles.delete.pre",
				"priority" => -64,
				"action" => function($app, $id) {
					$app->database->deleteRow("routes", \Crispage\Routing\Router::getArticleRoute($id));
				}
			)));

			$this->events->registerAction(new \Crispage\Events\EventAction(array(
				"id" => "crispage.categorysetroute",
				"event" => "assets.categories.set",
				"priority" => -64,
				"action" => function($app, $id) {
					$app->database->writeRow("routes", \Crispage\Routing\Router::getCategoryRoute($id), array("item_id" => $id, "view" => "core/category"));
				}
			)));

			$this->events->registerAction(new \Crispage\Events\EventAction(array(
				"id" => "crispage.categorydeleteroute",
				"event" => "assets.categories.delete.pre",
				"priority" => -64,
				"action" => function($app, $id) {
					$app->database->deleteRow("routes", \Crispage\Routing\Router::getCategoryRoute($id));
				}
			)));

			// Route the request
			\Crispage\Routing\Router::routeRequest($basepath, $default);
		}

		public function initDatabase(string $type, string $loc, string $name, array $options) {
			try {
				$this->database = new $type($loc, $name, $options);
				if (!$this->database) throw new \Exception("Database not initialized");
			} catch (\Throwable $e) {
				http_response_code(500);
				die("<h1>Database Configuration Error</h1>$e");
			}
		}

		public function loadPlugin(\Crispage\Assets\Plugin $plugin) : void {
			$this->events->trigger("app.plugins.pre_load", $plugin);
			try {
				$classname = \Crispage\Helpers\ExtensionHelper::loadClass(\Config::APPROOT . "/plugins/$plugin->class.php", "\\Crispage\\Plugins\\");
				$this->loadedPlugins[] = new $classname(array(
					"id"	=> $plugin->id,
					"class"	=> $plugin->class,
					"priority"	=> $plugin->priority,
					"created" => $plugin->created,
					"modified" => $plugin->modified,
					"options" => $plugin->options
				));
				$this->events->trigger("app.plugins.post_load", $plugin);
			} catch (\Throwable $e) {
				$this->events->trigger("app.plugins.error_load", $plugin, $e);
				throw new \Crispage\ApplicationException(500, $this("i18n")->getString("plugin_error"), $this("i18n")->getString("plugin_error_ex", null, $plugin->id), null, $e, false);
			}
		}

		public function loadPlugins(string $scope = "frontend") : void {
			if (count($this->loadedPlugins)) return;
			foreach ($this("plugins")->getAll(array("scope" => $scope)) as $plugin)
				$this->loadPlugin($plugin);
			usort($this->loadedPlugins, function($a, $b) {
				if ($a->priority == $b->priority) return 0;
				return ($a->priority < $b->priority) ? -1 : 1;
			});
		}

		public function executePlugins() : void {
			if ($this->pluginsExecd) return;
			$this->pluginsExecd = true;
			foreach ($this->loadedPlugins as $plugin) {
				$this->events->trigger("app.plugins.pre_exec", $plugin);
				try {
					$plugin->execute();
					$this->events->trigger("app.plugins.post_exec", $plugin);
				} catch (\Throwable $e) {
					$this->events->trigger("app.plugins.error_exec", $plugin, $e);
					throw new \Crispage\ApplicationException(500, $this("i18n")->getString("plugin_error"), $this("i18n")->getString("plugin_error_ex2", null, $plugin->id), null, $e, false);
				}
			}
		}

		public function loadLanguages() : void {
			foreach (glob(\Config::APPROOT . "/languages/*") as $file)
				$this("i18n")->loadLanguageFile($file);
		}

		protected abstract function request(\Crispage\Routing\Request $request) : void;

		public function error(\Throwable $e) : void {
			$this->hasError = true;
			if ($e instanceof \Crispage\ApplicationException) {
				$http = $e->getHttpStatus();
				$title = $e->getPageTitle();
				$lp = $e->loadPlugins();
			} else {
				$http = 500;
				$title = "Internal Server Error";
				$lp = true;
			}
			$body = $e->getMessage();

			$this->events->trigger("app.error", $e);
			http_response_code($http);
			$this->request = new \Crispage\Routing\Request(array("route" => array(), "slug" => \Crispage\Routing\Router::getSlug()));

			$this->events->trigger("app.error.pre_render", $e);

			$this->events->trigger("app.languages.pre_load");
			$this->loadLanguages();
			$this->events->trigger("app.languages.post_load");
			if ($lp) {
				$this->events->trigger("app.plugins.pre_load");
				$this->loadPlugins();
				$this->executePlugins();
				$this->events->trigger("app.plugins.post_load");
				$this->events->trigger("app.modules.pre_load");
				$this->page->loadModules();
				$this->events->trigger("app.modules.post_load");
			} else {
				$this->page->clearModules();
			}

			$this->page->setTitle($title);
			$this->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
			$this->page->metas["description"] = array("name" => "description", "content" => $this->getSetting("meta_desc", ""));
			$this->page->metas["keywords"] = array("name" => "keywords", "content" => $this->getSetting("meta_keys", ""));

			$content = "<div id=\"main\" class=\"page-content\">\n";
			$content .= "<p>$body</p>\n";
			if ($http >= 500) $content .= "<pre>" . $e->getTraceAsString() . "</pre>";
			if ($e->getPrevious()) $content .= "<pre>Previous Error:\n" . $e->getPrevious() . "\n</pre>";
			$content .= "</div>";

			$this->page->setContent($content);
			$this->renderPage();
			$this->events->trigger("app.error.post_render", $e);
			die();
		}

		public function redirect(string $url, bool $permanent = false) : void {
			$this->events->trigger("redirect", $url, $permanent, null);
			ob_clean();
			if ($permanent) http_response_code(301);
			else http_response_code(302);
			header("Location: $url");
			die("Redirecting...");
		}

		public function redirectWithMessages(string $url, array $messages) : void {
			$this->events->trigger("redirect", $url, false, $messages);
			ob_clean();
			if (isset($messages["type"]) && isset($messages["content"])) {
				$this->events->trigger("message", $messages);
				$this->page->setCookie("msg_" . $messages["type"], $messages["content"], time() + 1);
			} else {
				foreach ($messages as $message) {
					$this->events->trigger("message", $message);
					$this->page->setCookie("msg_" . ($message["type"] ?? "info"), $message["content"] ?? "", time() + 1);
				}
			}
			http_response_code(302);
			header("Location: " . \Config::WEBROOT . "$url");
			die("Redirecting...");
		}

		public function renderPage() : void {
			$this->events->trigger("app.page.pre_render");
			try {
				ob_clean();
				$this->template->render();
				$this->events->trigger("app.page.post_render");
			} catch (\Throwable $e) {
				$this->events->trigger("app.page.error_render", $e);
				throw new \Crispage\ApplicationException(500, $this("i18n")->getString("render_error"), $this("i18n")->getString("render_error_ex3"), null, $e, false);
			}
		}

		public function getSetting(string $key, string $default = null) : ?string {
			if (!isset($this->database)) return $default;
			return $this->database->readRow("settings", $key)["value"] ?? $default;
		}

		public function setSetting(string $key, string $value) : void {
			if (!isset($this->database)) return;
			$this->database->writeRow("settings", $key, array("value" => $value));
			$this->events->trigger("app.settings.set", $key, $value);
		}

		public function nameToId(string $name = null) : string {
			if ($name == null) return null;
			if (empty($name)) return null;
			$name = strtolower($name);
			$name = preg_replace("/[^a-z0-9_]/", "-", $name);
			$name = preg_replace("/--/", "-", $name);
	 		$name = trim($name, "-");
			return $name;
		}

		public function parseVersionString(string $vs = "0.0.0") : array {
			preg_match("/v?(\d+)(?:\.(\d+)(?:\.(\d+))?)?(?:[ \-]?([a-zA-Z0-9_]+))?/", $vs, $matches);
			return array(
				"major" => $matches[1] ?? 0,
				"minor" => $matches[2] ?? 0,
				"patch" => $matches[3] ?? 0,
				"stage" => $matches[4] ?? "release"
			);
		}
	}
?>
