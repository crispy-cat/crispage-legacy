<?php
	/*
		Crispage - A lightweight CMS for developers
		core/modules/Module.php - Module class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Module {
		public string $id;
		public string $title;
		public string $class;
		public string $pos;
		public int $ord;
		public int $created;
		public int $modified;
		public array $settings;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id = $data["id"];
			$this->title = $data["title"];
			$this->class = $data["class"];
			$this->pos = $data["pos"];
			$this->ord = $data["ord"];
			$this->created = $data["created"];
			$this->modified = $data["modified"];
			$this->settings = $data["settings"];
		}

		public function render() {}
	}
