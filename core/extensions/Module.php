<?php
	/*
		Crispage - A lightweight CMS for developers
		core/modules/Module.php - Module class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Module extends Asset {
		public string $title;
		public string $class;
		public string $pos;
		public int $ord;
		public string $scope;

		public function __construct(array $data) {
			parent::__construct("Module", $data);
			if (!is_array($data)) return;
			$this->title = $data["title"] ?? "";
			$this->class = $data["class"] ?? "";
			$this->pos = $data["pos"] ?? "";
			$this->ord = $data["ord"] ?? 0;
			$this->scope = $data["scope"] ?? "frontend";
		}

		public function render() {}
	}
