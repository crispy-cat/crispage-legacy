<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/classes/Article.php - Article class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Article extends Asset {
		public string $category;
		public string $state;
		public string $author;
		public string $title;
		public string $summary;
		public string $tags;
		public string $content;
		public string $meta_desc;
		public string $meta_keys;
		public string $meta_robots;
		public int $hits;

		public function __construct(array $data) {
			parent::__construct("Article", $data);
			if (!is_array($data)) return;
			$this->category = (string)($data["category"] ?? "");
			$this->state	= (string)($data["state"] ?? "");
			$this->author	= (string)($data["author"] ?? "");
			$this->title	= (string)($data["title"] ?? "");
			$this->summary	= (string)($data["summary"] ?? "");
			$this->tags		= (string)($data["tags"] ?? "");
			$this->content	= (string)($data["content"] ?? "");
			$this->meta_desc= (string)($data["meta_desc"] ?? "");
			$this->meta_keys= (string)($data["meta_keys"] ?? "");
			$this->meta_robots= (string)($data["meta_robots"] ?? "");
			$this->hits		= (int)($data["hits"] ?? 0);
		}
	}
?>
