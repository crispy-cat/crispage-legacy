<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Application.php - Application base class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/ApplicationException.php";
	require_once Config::APPROOT . "/core/events/EventManager.php";
	require_once Config::APPROOT . "/core/assets/ApplicationAssetManagers.php";

	require_once Config::APPROOT . "/core/Request.php";
	require_once Config::APPROOT . "/core/Page.php";
	require_once Config::APPROOT . "/core/Template.php";

	require_once Config::APPROOT . "/core/database/Database.php";
	require_once Config::APPROOT . "/core/database/JSONDatabase.php";

	require_once Config::APPROOT . "/core/helpers/Paginator.php";
	require_once Config::APPROOT . "/core/helpers/RenderHelper.php";
	require_once Config::APPROOT . "/core/helpers/Randomizer.php";
	require_once Config::APPROOT . "/core/helpers/Mailer.php";
	require_once Config::APPROOT . "/core/helpers/FileHelper.php";
	require_once Config::APPROOT . "/core/helpers/ExtensionHelper.php";
	require_once Config::APPROOT . "/core/helpers/FormHelper.php";

	require_once Config::APPROOT . "/core/users/UserPermissions.php";
	require_once Config::APPROOT . "/core/users/Authenticator.php";
	require_once Config::APPROOT . "/core/i18n/I18n.php";

	require_once Config::APPROOT . "/core/assets/classes/Article.php";
	require_once Config::APPROOT . "/core/assets/classes/Category.php";
	require_once Config::APPROOT . "/core/assets/classes/Comment.php";
	require_once Config::APPROOT . "/core/assets/classes/Menu.php";
	require_once Config::APPROOT . "/core/assets/classes/MenuItem.php";
	require_once Config::APPROOT . "/core/assets/classes/Module.php";
	require_once Config::APPROOT . "/core/assets/classes/Plugin.php";
	require_once Config::APPROOT . "/core/assets/classes/User.php";
	require_once Config::APPROOT . "/core/assets/classes/UserGroup.php";
	require_once Config::APPROOT . "/core/assets/classes/Session.php";
	require_once Config::APPROOT . "/core/assets/classes/Ban.php";

	abstract class Application {
		public Request $request;
		public Page $page;
		public Template $template;

		public Database $database;
		public EventManager $events;

		public ApplicationAssetManagers $assets;

		public Authenticator $auth;
		public I18n $i18n;

		public array $loadedPlugins	= array();
		public bool $pluginsExecd	= false;
		public array $vars			= array();

		public function __construct() {
			try {
				$dbtype = Config::DB_TYPE;
				$this->database = new $dbtype(Config::DB_JSON_LOC, Config::DB_JSON_NAME, Config::DB_JSON_PRETTY);
			} catch (Throwable $e) {
				die("Invalid database type; please check config\n$e");
			}

			$this->page = new Page();
			$this->events = new EventManager();
			$this->auth = new Authenticator();
			$this->i18n = new I18n();

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

			require_once Config::APPROOT . "/core/events/defaultevents.php";
		}

		public function __invoke(string $name) {
			if (isset($this->$name)) return $this->$name;
			$am = ($this->assets)($name);
			if ($am) return $am;
			return $this->vars[$name] ?? null;
		}

		public function __destruct() {
			if (isset($this->database)) $this->database->writeChanges();
		}

		public function loadPlugin(Plugin $plugin) : void {
			$this->events->trigger("app.plugins.pre_load", $plugin);
			try {
				$classname = ExtensionHelper::loadClass(Config::APPROOT . "/plugins/$plugin->class.php");
				$this->loadedPlugins[] = new $classname(array(
					"id"	=> $plugin->id,
					"class"	=> $plugin->class,
					"priority"	=> $plugin->priority,
					"created" => $plugin->created,
					"modified" => $plugin->modified,
					"options" => $plugin->options
				));
				$this->events->trigger("app.plugins.post_load", $plugin);
			} catch (Throwable $e) {
				throw new ApplicationException(500, $this("i18n")->getString("plugin_error"), $this("i18n")->getString("plugin_error_ex", null, $plugin->id), null, $e, false);
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
			$this->pluginsExeced = true;
			foreach ($this->loadedPlugins as $plugin) {
				try {
					$plugin->execute();
				} catch (Throwable $e) {
					throw new ApplicationException(500, $this("i18n")->getString("plugin_error"), $this("i18n")->getString("plugin_error_ex2", null, $plugin->id), null, $e, false);
				}
			}
		}

		public function loadLanguages() : void {
			foreach (glob(Config::APPROOT . "/languages/*") as $file)
				$this("i18n")->loadLanguageFile($file);
		}

		protected abstract function request(Request $request) : void;

		public function error(Throwable $e) : void {
			if ($e instanceof ApplicationException) {
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
			$this->request = new Request(array("route" => array(), "slug" => Router::getSlug()));

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
				$this->page->setCookie("msg_" . $messages["type"], $messages["content"]);
			} else {
				foreach ($messages as $message) {
					$this->events->trigger("message", $message);
					$this->page->setCookie("msg_" . ($message["type"] ?? "info"), $message["content"] ?? "");
				}
			}
			http_response_code(302);
			header("Location: " . Config::WEBROOT . "$url");
			die("Redirecting...");
		}

		public function renderPage() : void {
			$this->events->trigger("app.page.pre_render");
			try {
				ob_clean();
				$this->template->render();
			} catch (Throwable $e) {
				throw new ApplicationException(500, $this("i18n")->getString("render_error"), $this("i18n")->getString("render_error_ex3"), null, $e, false);
			}
			$this->events->trigger("app.page.post_render");
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
	}
?>
