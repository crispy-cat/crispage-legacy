<?php
	/*
		Crispage - A lightweight CMS for developers
		core/content/Category.php - Category class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Category extends Asset {
		public string $parent;
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
			$this->parent	= $data["parent"] ?? "";
			$this->state	= $data["state"] ?? "";
			$this->title	= $data["title"] ?? "";
			$this->content	= $data["content"] ?? "";
			$this->tags		= $data["tags"] ?? "";
			$this->meta_desc= $data["meta_desc"] ?? "";
			$this->meta_keys= $data["meta_keys"] ?? "";
			$this->meta_robots=$data["meta_robots"] ?? "";
		}

		public static function categoryParentLoop(string $id = null) : bool {
			global $app;
			if ($id == null) return false;

			$names = array();

			$parent = ($app->assets)("categories")->get($id)->parent;
			if ($parent == null) return false;

			while ($parent !== null) {
				if (in_array($parent, $names)) return true;
				array_push($names, $parent);
				$category = ($app->assets)("categories")->get($parent);
				if (!$category) return false;
				$parent = $category->parent;
			}

			return false;
		}
	}
?>
