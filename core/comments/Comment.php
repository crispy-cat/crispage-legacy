<?php
	/*
		Crispage - A lightweight CMS for developers
		core/content/Comment.php - Comment class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Comment {
		public string $id;
		public string $article;
		public int $created;
		public int $modified;
		public string $author;
		public string $content;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id		= $data["id"];
			$this->article = $data["article"];
			$this->created	= $data["created"];
			$this->modified	= $data["modified"];
			$this->author	= $data["author"];
			$this->content	= $data["content"];
		}
	}
?>
