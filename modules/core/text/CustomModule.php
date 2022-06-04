<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/text/CustomModule.php - Custom text module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Modules;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class CustomModule extends \Crispage\Assets\Module {
		public function render() {
			if (!isset($this->options["content"])) return;

			echo "<div class=\"module CustomModule module-$this->id\">\n";
			echo $this->options["content"] . "\n";
			echo "</div>";
		}
	}
?>
