<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/classes/Module.php - Module class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Assets;

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
			$this->title = (string)($data["title"] ?? "");
			$this->class = (string)($data["class"] ?? "");
			$this->pos = (string)($data["pos"] ?? "");
			$this->ord = (int)($data["ord"] ?? 0);
			$this->scope = (string)($data["scope"] ?? "frontend");
		}

		public function render() {}
	}
