<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/classes/Comment.php - Comment class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Comment extends Asset {
		public string $article;
		public string $author;
		public string $content;

		public function __construct(array $data) {
			parent::__construct("Comment", $data);
			if (!is_array($data)) return;
			$this->article = (string)($data["article"] ?? "");
			$this->author	= (string)($data["author"] ?? "");
			$this->content	= (string)($data["content"] ?? "");
		}
	}
?>
