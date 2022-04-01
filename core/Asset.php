<?php
	/*
		Crispage - A lightweight CMS for developers
		core/Asset.php - Asset class

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
			$this->uid = $data["uid"] ?? (int)hexdec(md5(time()));
			$this->id = $data["id"] ?? "";
			$this->created = $data["created"] ?? time();
			$this->modified = $data["modified"] ?? time();
			$this->options = $data["options"] ?? array();
		}
	}
