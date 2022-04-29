<?php
	/*
		Crispage - A lightweight CMS for developers
		core/menus/Menu.php - Menu class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Menu extends Asset {
		public string $title;

		public function __construct(array $data) {
			parent::__construct("Menu", $data);
			if (!is_array($data)) return;
			$this->title = $data["title"] ?? "";
		}
	}
?>
