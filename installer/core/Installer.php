<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/core/Installer.php - Installer application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/ApplicationBase.php";

	class Installer extends ApplicationBase {
		public ExtensionManager $extensions;

		public function __construct() {
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

			$this->template = new Template(array("backend" => true, "template_name" => "installer"));
			$this->extensions = new ExtensionManager();
		}

		public function initDatabase(string $type, array $options) {
			$this->database = new JSONDatabase($options["location"], $options["name"], $options["pretty"]);
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
				throw new ApplicationException(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", null, $e, false);
			}
		}

		public function error(Throwable $e) {
			if ($e instanceof ApplicationException)
				parent::error(new ApplicationException($e->getHttpStatus(), $e->getPageTitle(), $e->getMessage(), null, $e, false));
			else
				parent::error(new ApplicationException(500, "Internal Server Error", $e->getMessage(), null, $e, false));
		}
	}
?>
