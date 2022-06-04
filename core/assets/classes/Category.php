<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/classes/Category.php - Category class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Category extends Asset {
		public ?string $parent;
		public string $state;
		public string $title;
		public string $content;
		public string $tags;
		public string $meta_desc;
		public string $meta_keys;
		public string $meta_robots;

		public function __construct($data) {
			parent::__construct("Category", $data);
			if (!is_array($data)) return;
			$this->parent	= (string)($data["parent"] ?? "");
			$this->state	= (string)($data["state"] ?? "");
			$this->title	= (string)($data["title"] ?? "");
			$this->content	= (string)($data["content"] ?? "");
			$this->tags		= (string)($data["tags"] ?? "");
			$this->meta_desc= (string)($data["meta_desc"] ?? "");
			$this->meta_keys= (string)($data["meta_keys"] ?? "");
			$this->meta_robots=(string)($data["meta_robots"] ?? "");
		}
	}
?>
