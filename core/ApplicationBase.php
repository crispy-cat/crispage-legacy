<?php
	/*
		Crispage - A lightweight CMS for developers
		core/ApplicationBase.php - Application base class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/database/Database.php";
	require_once Config::APPROOT . "/core/events/EventManager.php";
	require_once Config::APPROOT . "/core/database/JSONDatabase.php";
	require_once Config::APPROOT . "/core/Template.php";
	require_once Config::APPROOT . "/core/modules/ModuleManager.php";
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

	abstract class ApplicationBase {
		public Database $database;
		public EventManager $events;
		public Page $page;
		public ModuleManager $modules;
		public ContentManager $content;
		public CommentManager $comments;
		public MenuManager $menus;
		public UserManager $users;
		public SessionManager $session;
		public Authenticator $auth;
		public BanManager $bans;
		public Request $request;
		public Template $template;
		public array $vars = array();

		public function __construct() {
			try {
				$dbtype = Config::DB_TYPE;
				$this->database = new $dbtype();
			} catch (Throwable $e) {
				die("Invalid database type; please check config\n$e");
			}
			$this->events = new EventManager();
			$this->page = new Page();
			$this->modules = new ModuleManager();
			$this->content = new ContentManager();
			$this->comments = new CommentManager();
			$this->menus = new MenuManager();
			$this->users = new UserManager();
			$this->session = new SessionManager();
			$this->auth = new Authenticator();
			$this->bans = new BanManager();
		}

		protected abstract function request(Request $request);

		public function error(int $http, string $title, string $body, Throwable $e = null) {
			$this->events->trigger("app.error", $http, $title, $body, $e);
			http_response_code($http);
			$this->request = new Request(array("route" => array(), "slug" => Router::getSlug(get_called_class() == "Backend")));
			$this->page->loadModules();

			$this->page->setTitle($title);
			$this->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
			$this->page->metas["description"] = array("name" => "description", "content" => $this->getSetting("meta_desc", ""));
			$this->page->metas["keywords"] = array("name" => "keywords", "content" => $this->getSetting("meta_keys", ""));

			$content = "<div id=\"main\" class=\"page-content\">\n";
			$content .= "<p>$body</p>\n";
			if (isset($e)) $content .= "<pre>\n$e\n</pre>";
			$content .= "</div>";

			$this->page->setContent($content);

			$this->events->trigger("app.error.pre_render", $http, $title, $body, $e);

			$this->renderPage();
		}

		public function redirect(string $url, bool $permanent = false) {
			$this->events->trigger("redirect", $url, $permanent);
			if ($permanent) http_response_code(301);
			else http_response_code(302);
			header("Location: $url");
			die("Redirecting...");
		}

		public function renderPage() {
			$this->events->trigger("app.page.pre_render");
			try {
				$this->template->render();
			} catch (Throwable $e) {
				$this->error(500, "An error occurred", "The page could not be rendered: ", $e);
			}
		}

		public function getSetting(string $key, string $default = null) {
			return $this->database->readRow("settings", $key)["value"] ?? $default;
		}

		public function setSetting(string $key, string $value) {
			$this->database->writeRow("settings", $key, array("value" => $value));
			$this->events->trigger("app.settings.set", $key, $value);
		}

		public function nameToId(string $name = null) {
			if ($name == null) return null;
			$name = strtolower($name);
			$name = preg_replace("/[^a-z0-9_]/", "-", $name);
			$name = preg_replace("/--/", "-", $name);
	 		$name = trim($name, "-");
			return $name;
		}
	}
?>
