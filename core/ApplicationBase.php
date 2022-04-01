<?php
	/*
		Crispage - A lightweight CMS for developers
		core/ApplicationBase.php - Application base class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/ApplicationException.php";
	require_once Config::APPROOT . "/core/database/Database.php";
	require_once Config::APPROOT . "/core/events/EventManager.php";
	require_once Config::APPROOT . "/core/database/JSONDatabase.php";
	require_once Config::APPROOT . "/core/Template.php";
	require_once Config::APPROOT . "/core/Asset.php";
	require_once Config::APPROOT . "/core/extensions/ExtensionManager.php";
	require_once Config::APPROOT . "/core/content/ContentManager.php";
	require_once Config::APPROOT . "/core/comments/CommentManager.php";
	require_once Config::APPROOT . "/core/menus/MenuManager.php";
	require_once Config::APPROOT . "/core/users/UserManager.php";
	require_once Config::APPROOT . "/core/users/SessionManager.php";
	require_once Config::APPROOT . "/core/users/Authenticator.php";
	require_once Config::APPROOT . "/core/users/BanManager.php";
	require_once Config::APPROOT . "/core/Request.php";
	require_once Config::APPROOT . "/core/Page.php";
	require_once Config::APPROOT . "/core/helpers/Paginator.php";
	require_once Config::APPROOT . "/core/helpers/RenderHelper.php";
	require_once Config::APPROOT . "/core/helpers/Randomizer.php";
	require_once Config::APPROOT . "/core/helpers/Mailer.php";
	require_once Config::APPROOT . "/core/helpers/FileHelper.php";

	abstract class ApplicationBase {
		public Database $database;
		public EventManager $events;
		public Page $page;
		public ExtensionManager $extensions;
		public ContentManager $content;
		public CommentManager $comments;
		public MenuManager $menus;
		public UserManager $users;
		public SessionManager $session;
		public Authenticator $auth;
		public BanManager $bans;
		public Request $request;
		public Template $template;
		public array $loadedPlugins	= array();
		public array $vars			= array();
		protected bool $pageRendered = false;

		public function __construct() {
			try {
				$dbtype = Config::DB_TYPE;
				$this->database = new $dbtype(Config::DB_JSON_LOC, Config::DB_JSON_NAME, Config::DB_JSON_PRETTY);
			} catch (Throwable $e) {
				die("Invalid database type; please check config\n$e");
			}
			$this->events = new EventManager();
			$this->page = new Page();
			$this->extensions = new ExtensionManager();
			$this->content = new ContentManager();
			$this->comments = new CommentManager();
			$this->menus = new MenuManager();
			$this->users = new UserManager();
			$this->session = new SessionManager();
			$this->auth = new Authenticator();
			$this->bans = new BanManager();
		}

		public function __destruct() {
			if (isset($this->database)) $this->database->writeChanges();
		}

		public function loadPlugin(Plugin $plugin) {
			global $app;
			$app->events->trigger("page.plugins.pre_load", $plugin);
			try {
				if (!file_exists(Config::APPROOT . "/plugins/$plugin->class.php")) return;
				include_once Config::APPROOT . "/plugins/$plugin->class.php";
				@$classname = array_pop(explode("/", $plugin->class));
				if (!class_exists($classname)) return;
				$this->loadedPlugins[] = new $classname(array(
					"id"	=> $plugin->id,
					"class"	=> $plugin->class,
					"priority"	=> $plugin->priority,
					"created" => $plugin->created,
					"modified" => $plugin->modified,
					"options" => $plugin->options
				));
			} catch (Throwable $e) {
				throw new ApplicationException(500, "An error occurred", "Plugin <code>$plugin->id</code> could not be loaded: ", null, $e, false);
			}
		}

		public function loadPlugins(string $scope = "frontend") {
			if (count($this->loadedPlugins)) return;
			foreach ($this->extensions->gPlugins($scope) as $plugin)
				$this->loadPlugin($plugin);
			usort($this->loadedPlugins, function($a, $b) {
				if ($a->priority == $b->priority) return 0;
				return ($a->priority < $b->priority) ? -1 : 1;
			});

			foreach ($this->loadedPlugins as $plugin) {
				try {
					$plugin->execute();
				} catch (Throwable $e) {
					throw new ApplicationException(500, "An error occurred", "Plugin <code>$plugin->id</code> could not be executed: ", null, $e, false);
				}
			}
		}

		protected abstract function request(Request $request);

		public function error(Throwable $e) {
			if ($e instanceof ApplicationException) {
				$http = $e->getHttpStatus();
				$title = $e->getPageTitle();
			} else {
				$http = 500;
				$title = "Internal Server Error";
			}
			$body = $e->getMessage();
			
			$this->events->trigger("app.error", $e);
			http_response_code($http);
			$this->request = new Request(array("route" => array(), "slug" => Router::getSlug(get_called_class() == "Backend")));
			
			$this->events->trigger("app.error.pre_render", $e);
			
			if (!$this->pageRendered) {
				if ($e instanceof ApplicationException && $e->loadPlugins()) {
					$this->events->trigger("app.plugins.pre_load");
					$this->loadPlugins();
					$this->events->trigger("app.plugins.post_load");
					$this->events->trigger("app.modules.pre_load");
					$this->page->loadModules();
					$this->events->trigger("app.modules.post_load");
				}
	
				$this->page->setTitle($title);
				$this->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
				$this->page->metas["description"] = array("name" => "description", "content" => $this->getSetting("meta_desc", ""));
				$this->page->metas["keywords"] = array("name" => "keywords", "content" => $this->getSetting("meta_keys", ""));
	
				$content = "<div id=\"main\" class=\"page-content\">\n";
				$content .= "<p>$body</p>\n";
				if ($e->getPrevious()) $content .= "<pre>\n" . $e->getPrevious() . "\n</pre>";
				$content .= "</div>";
	
				$this->page->setContent($content);
				$this->renderPage();
			} else {
				die($e);
			}
		}

		public function redirect(string $url, bool $permanent = false) {
			$this->events->trigger("redirect", $url, $permanent);
			if ($permanent) http_response_code(301);
			else http_response_code(302);
			header("Location: $url");
			die("Redirecting...");
		}

		public function redirectWithMessages(string $url, array $messages) {
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

		public function renderPage() {
			if ($this->pageRendered) return;
			$this->pageRendered = true;
			$this->events->trigger("app.page.pre_render");
			try {
				$this->template->render();
			} catch (Throwable $e) {
				throw new ApplicationException(500, "An error occurred", "The page could not be rendered: ", null, $e);
			}
			$this->events->trigger("app.page.post_render");
		}

		public function getSetting(string $key, string $default = null) : ?string {
			if (!isset($this->database)) return $default;
			return $this->database->readRow("settings", $key)["value"] ?? $default;
		}

		public function setSetting(string $key, string $value) {
			if (!isset($this->database)) return;
			$this->database->writeRow("settings", $key, array("value" => $value));
			$this->events->trigger("app.settings.set", $key, $value);
		}

		public function nameToId(string $name = null) : string {
			if ($name == null) return null;
			$name = strtolower($name);
			$name = preg_replace("/[^a-z0-9_]/", "-", $name);
			$name = preg_replace("/--/", "-", $name);
	 		$name = trim($name, "-");
			return $name;
		}
	}
?>
