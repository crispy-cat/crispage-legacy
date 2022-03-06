<?php
	/*
		Crispage - A lightweight CMS for developers
		core/content/Article.php - Article class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Article {
		public string $id;
		public string $category;
		public string $state;
		public int $created;
		public int $modified;
		public string $author;
		public string $title;
		public string $summary;
		public string $tags;
		public string $content;
		public string $meta_desc;
		public string $meta_keys;
		public string $meta_robots;
		public int $hits;
		public array $options;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id		= $data["id"];
			$this->category = $data["category"];
			$this->state	= $data["state"];
			$this->created	= $data["created"];
			$this->modified	= $data["modified"];
			$this->author	= $data["author"];
			$this->title	= $data["title"];
			$this->summary	= $data["summary"];
			$this->tags		= $data["tags"];
			$this->content	= $data["content"];
			$this->meta_desc= $data["meta_desc"];
			$this->meta_keys= $data["meta_keys"];
			$this->meta_robots= $data["meta_robots"];
			$this->hits		= $data["hits"];
			$this->options	= $data["options"];
		}
	}
?>
