<?php
	/*
		Crispage - A lightweight CMS for developers
		core/menus/Menu.php - Menu class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Menu {
		public string $id;
		public string $title;
		public int $created;
		public int $modified;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id = $data["id"];
			$this->title = $data["title"];
			$this->created = $data["created"];
			$this->modified = $data["modified"];
		}
	}
?>
