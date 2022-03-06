<?php
	/*
		Crispage - A lightweight CMS for developers
		core/content/Category.php - Category class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Category {
		public string $id;
		public ?string $parent;
		public string $state;
		public int $created;
		public int $modified;
		public string $title;
		public string $content;
		public string $tags;
		public string $meta_desc;
		public string $meta_keys;
		public string $meta_robots;
		public array $options;

		public function __construct($data) {
			if (!is_array($data)) return;
			$this->id		= $data["id"];
			$this->parent	= $data["parent"];
			$this->state	= $data["state"];
			$this->created	= $data["created"];
			$this->modified	= $data["modified"];
			$this->title	= $data["title"];
			$this->content	= $data["content"];
			$this->tags		= $data["tags"];
			$this->meta_desc= $data["meta_desc"];
			$this->meta_keys= $data["meta_keys"];
			$this->meta_robots=$data["meta_robots"];
			$this->options	= $data["options"];
		}
	}
?>
