<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/Asset.php - Asset class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.1.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Asset {
		public int $uid;
		public string $id;
		public string $className;
		public int $created;
		public int $modified;
		public array $options;

		public function __construct($class, array $data) {
			if (!is_array($data)) return;
			$this->className = $class;
			$this->uid = $data["uid"] ?? (int)hexdec(md5(time() + random_int(PHP_INT_MIN, PHP_INT_MAX)));
			$this->id = $data["id"] ?? "";
			$this->created = $data["created"] ?? time();
			$this->modified = $data["modified"] ?? time();
			$this->options = $data["options"] ?? array();
		}
		
		public function toDatabaseObject() : array {
			$props = array();
			foreach ($this as $k => $v) $props[$k] = $v;
			return $props;
		}
		
		public static function parentLoop(string $table, string $id = null) : bool {
			global $app;
			if (!$id) return false;

			$names = array();

			@$parent = ($app->assets)($table)->get($id)->parent;
			if (!$parent) return false;

			while ($parent !== null) {
				if (in_array($parent, $names)) return true;
				array_push($names, $parent);
				$category = $app($table)->get($parent);
				if (!$category) return false;
				$parent = $category->parent;
			}

			return false;
		}
		
		public static function nestingLevel(string $table, string $id = null) : int {
			global $app;
			if (!$id) return -1;
			
			$count = 0;
			@$parent = $app($table)->get($id)->parent;
			
			while (true) {
				if (!$parent) return $count;
				$count++;
			}
		}
	}
