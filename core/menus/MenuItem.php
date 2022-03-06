<?php
	/*
		Crispage - A lightweight CMS for developers
		core/menus/MenuItem.php - Menu item class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class MenuItem {
		public string $id;
		public string $label;
		public string $type;
		public string $menu;
		public ?string $parent;
		public int $ord;
		public $content;
		public int $created;
		public int $modified;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id = $data["id"];
			$this->label = $data["label"];
			$this->type = $data["type"];
			$this->menu = $data["menu"];
			$this->parent = $data["parent"];
			$this->ord = $data["ord"];
			$this->content = $data["content"];
			$this->created = $data["created"];
			$this->modified = $data["modified"];
		}

		public function getUrl() {
			global $app;
			switch ($this->type) {
				case "article":
					return Router::getArticleRoute($this->content);
				case "category":
					return Router::getCategoryRoute($this->content);
				case "url":
					return $this->content;
				default:
					return $this->type;
			}
		}
	}
?>
