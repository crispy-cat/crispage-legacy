<?php
	/*
		Crispage - A lightweight CMS for developers
		core/menus/MenuItem.php - Menu item class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class MenuItem extends Asset {
		public string $label;
		public string $type;
		public string $menu;
		public string $parent;
		public int $ord;
		public $content;

		public function __construct(array $data) {
			parent::__construct("MenuItem", $data);
			if (!is_array($data)) return;
			$this->label = $data["label"] ?? "";
			$this->type = $data["type"] ?? "url";
			$this->menu = $data["menu"] ?? "";
			$this->parent = $data["parent"] ?? "";
			$this->ord = $data["ord"] ?? 0;
			$this->content = $data["content"] ?? "";
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

		public static function menuItemParentLoop(string $id = null) : bool {
			global $app;
			if ($id == null) return false;

			$names = array();

			$parent = ($app->assets)("menu_items")->get($id)->parent;

			while ($parent !== null) {
				if (in_array($parent, $names)) return true;
				array_push($names, $parent);
				$parent = ($app->assets)("menu_items")->get($parent)->parent;
			}

			return false;
		}
	}
?>
