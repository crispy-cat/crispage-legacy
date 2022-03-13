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
				include_once $this->directory . "/index.php";
			} else {
				$dir = $this->directory;
				$this->directory = Config::APPROOT . "/templates/system";
				throw new Exception("Template '$dir' does not exist!");
			}
		}
	}
?>