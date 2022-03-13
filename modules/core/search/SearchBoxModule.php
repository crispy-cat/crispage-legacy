<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/search/SearchBoxModule.php - Search box module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class SearchBoxModule extends Module {
		public function render() {
			echo "<form class=\"module SearchBoxModule module-$this->id {$this->options["classes"]}\" action=\"" . Config::WEBROOT . "/search\">\n";
			echo "<div class=\"input-group\">";
			echo "<input class=\"form-control\" type=\"search\" name=\"q\" placeholder=\"Search...\" />\n";
			echo "<button class=\"btn btn-primary\" type=\"submit\"><i class=\"bi bi-search\"></i></button>\n";
			echo "</div></form>";
		}
	}
?>
