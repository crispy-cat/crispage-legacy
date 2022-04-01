<?php
	/*
		Crispage - A lightweight CMS for developers
		core/menus/MenuManager.php - Menu manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/menus/Menu.php";
	require_once Config::APPROOT . "/core/menus/MenuItem.php";

	class MenuManager {
		public function getMenu(string $id = null) : ?Menu {
			if ($id == null) return null;
			global $app;

			$menu = $app->database->readRow("menus", $id);
			if (!$menu) return null;

			$menu = new Menu($menu);

			return $menu;
		}

		public function getMenuItem(string $id = null) : ?MenuItem {
			if ($id == null) return null;
			global $app;

			$item = $app->database->readRow("menuitems", $id);
			if (!$item) return null;

			$item = new MenuItem($item);
			return $item;
		}

		public function setMenu(string $id, Menu $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("menus", $id, array(
				"title" => $data->title,
				"created" => $data->created,
				"modified" => $data->modified
			));
			$app->events->trigger("menus.menu_set", $id);
		}

		public function setMenuItem(string $id, MenuItem $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("menuitems", $id, array(
				"label"		=> $data->label,
				"type"		=> $data->type,
				"menu"		=> $data->menu,
				"parent"	=> $data->parent,
				"ord"		=> $data->ord,
				"content"	=> $data->content,
				"created"	=> $data->created,
				"modified"	=> $data->modified
			));
			$app->events->trigger("menus.item_set", $id);
		}

		public function deleteMenu(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("menus", $id);
			$app->events->trigger("menus.menu_delete", $id);
		}

		public function deleteMenuItem(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("menuitems", $id);
			$app->events->trigger("menus.menu_item_delete", $id);
		}

		public function existsMenu(string $id) : bool {
			if ($id == null) return false;
			global $app;

			return $app->database->existsRow("menus", $id);
		}

		public function existsMenuItem(string $id) : bool {
			if ($id == null) return false;
			global $app;

			return $app->database->existsRow("menuitems", $id);
		}
		
		public function gMenus() : Generator {
			global $app;
			
			$dbmenus = $app->database->readRows("menus");
			
			foreach ($dbmenus as $menu)
				yield new Menu($menu);
		}
		
		public function gMenuItems(string $menu = null) : Generator {
			global $app;
			
			if ($menu) $dbitems = $app->database->readRows("menuitems", array("menu" => $menu));
			else $dbitems = $app->database->readRows("menuitems");
			
			foreach ($dbitems as $item)
				yield new MenuItem($item);
		}

		public function getMenus() : array {
			$menus = array();
		
			foreach ($this->gMenus() as $menu)
				$menus[] = $menu;

			return $menus;
		}

		public function getMenuItems(string $menu = null) : array {
			$items = array();

			foreach ($this->gMenuItems($menu) as $item)
				$items[] = $item;

			return $items;
		}

		public function menuItemParentLoop(string $id = null) : bool {
			if ($id == null) return false;

			$names = array();

			$parent = $this->getMenuItem($id)->parent;

			while ($parent !== null) {
				if (in_array($parent, $names)) return true;
				array_push($names, $parent);
				$parent = $this->getMenuItem($parent)->parent;
			}

			return false;
		}
	}
?>
