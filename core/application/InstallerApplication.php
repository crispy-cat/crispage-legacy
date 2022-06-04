<?php
	/*
		Crispage - A lightweight CMS for developers
		core/installer/InstallerApplication.php - Installer application class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	namespace Crispage\Application;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	define("IMSG_SUCCESS",	-2);
	define("IMSG_INFO",		-1);
	define("IMSG_NORMAL",	 0);
	define("IMSG_WARNING",	 1);
	define("IMSG_ERROR",	 2);

	require_once \Config::APPROOT . "/core/application/Application.php";

	class InstallerApplication extends Application {
		public const IMSG_SUCCESS =	IMSG_SUCCESS;
		public const IMSG_INFO =	IMSG_INFO;
		public const IMSG_NORMAL =	IMSG_NORMAL;
		public const IMSG_WARNING =	IMSG_WARNING;
		public const IMSG_ERROR =	IMSG_ERROR;

		public function __construct() {
			parent::__construct();

			$this->template = new \Crispage\Render\Template(array("backend" => true, "template_name" => "installer"));
		}

		public function request(\Crispage\Routing\Request $request)  : void {
			$this->events->trigger("app.installer.request", $request);
			$this->request = $request;
			$this->events->trigger("app.languages.pre_load");
			$this->loadLanguages();
			$this->events->trigger("app.languages.post_load");
			try {
				$app = $this;
				if (file_exists(\Config::APPROOT . "/installer/views/$request->slug.php"))
					include_once \Config::APPROOT . "/installer/views/$request->slug.php";
				else
					throw new \Exception("No view '$request->slug' exists!");
			} catch (\Throwable $e) {
				throw new \Crispage\ApplicationException(500, "An error occurred", "A server error has occurred and the page you requested is not available. Please try again later.", null, $e, false);
			}
		}

		public function error(\Throwable $e) : void {
			if ($e instanceof \Crispage\ApplicationException)
				parent::error(new \Crispage\ApplicationException($e->getHttpStatus(), $e->getPageTitle(), $e->getMessage(), null, $e, false));
			else
				parent::error(new Crispage\ApplicationException(500, "Internal Server Error", $e->getMessage(), null, $e, false));
		}

		public function installerMessage(string $msg, int $type = self::IMSG_NORMAL) {
			switch ($type) {
				case self::IMSG_SUCCESS:
					$color = "#008800";
					$el = "b";
					break;
				case self::IMSG_INFO:
					$color = "#3300ff";
					$el = "i";
					break;
				case self::IMSG_WARNING:
					$color = "#ff8800";
					$el = "b";
					break;
				case self::IMSG_ERROR:
					$color = "#ff0000";
					$el = "b";
					break;
				default:
					$color = "#000000";
					$el = "span";
			}
			echo "<$el style=\"color: $color;\">$msg</$el><br />";
			if ($type == IMSG_ERROR) throw new \Exception("The installer encountered an error.");
		}
	}
?>
