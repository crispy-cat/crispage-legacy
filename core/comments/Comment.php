<?php
	/*
		Crispage - A lightweight CMS for developers
		core/content/Comment.php - Comment class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Comment extends Asset {
		public string $article;
		public string $author;
		public string $content;

		public function __construct(array $data) {
			parent::__construct("Comment", $data);
			if (!is_array($data)) return;
			$this->article = $data["article"] ?? "";
			$this->author	= $data["author"] ?? "";
			$this->content	= $data["content"] ?? "";
		}
	}
?>
