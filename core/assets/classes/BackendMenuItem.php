<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/classes/BackendMenuItem.php - Backend menu item class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.7.0
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once \Config::APPROOT . "/core/assets/Asset.php";

	class BackendMenuItem extends Asset {
		public string $label;
		public string $url;
		public string $parent;
		public int $ord;

		public function __construct(array $data) {
			parent::__construct("BackendMenuItem", $data);
			if (!is_array($data)) return;
			$this->label = (string)($data["label"] ?? "");
			$this->url = (string)($data["url"] ?? "#");
			$this->parent = (string)($data["parent"] ?? "");
			$this->ord = (int)($data["ord"] ?? 0);
		}
	}
?>
