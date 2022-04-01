<?php
	/*
		Crispage - A lightweight CMS for developers
		backend.core/BackendMenuItem.php - Backend menu item class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.7.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class BackendMenuItem extends Asset {
		public string $label;
		public string $url;
		public string $parent;
		public int $ord;

		public function __construct(array $data) {
			parent::__construct("BackendMenuItem", $data);
			if (!is_array($data)) return;
			$this->label = $data["label"] ?? "";
			$this->url = $data["url"] ?? "#";
			$this->parent = $data["parent"] ?? "";
			$this->ord = $data["ord"] ?? 0;
		}
	}
?>
