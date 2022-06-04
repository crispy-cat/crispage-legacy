<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/classes/MenuItem.php - Menu item class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class MenuItem extends Asset {
		public string $label;
		public string $type;
		public string $menu;
		public ?string $parent;
		public int $ord;
		public $content;

		public function __construct(array $data) {
			parent::__construct("MenuItem", $data);
			if (!is_array($data)) return;
			$this->label = (string)($data["label"] ?? "");
			$this->type = (string)($data["type"] ?? "url");
			$this->menu = (string)($data["menu"] ?? "");
			$this->parent = (string)($data["parent"] ?? "");
			$this->ord = (int)($data["ord"] ?? 0);
			$this->content = (string)($data["content"] ?? "");
		}

		public function getUrl() {
			global $app;
			switch ($this->type) {
				case "article":
					return \Crispage\Routing\Router::getArticleRoute($this->content);
				case "category":
					return \Crispage\Routing\Router::getCategoryRoute($this->content);
				case "url":
					return $this->content;
				default:
					return $this->type;
			}
		}
	}
?>
