<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Template.php - Template class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Template {
		public string $directory;

		public function __construct(array $data) {
			$this->directory = Config::APPROOT . "/templates/" . $data["template_name"];
		}

		public function render() {
			global $app;
			if (file_exists($this->directory . "/index.php")) {
				try {
					include $this->directory . "/index.php";
				} catch (Throwable $e) {
					$app->error($e);
				}
			} else {
				$dir = $this->directory;
				$this->directory = Config::APPROOT . "/templates/system";
				$app->error(new ApplicationException(500, "Missing template", "Template '$dir' does not exist!", null, null, false));
			}
		}
	}
?>
