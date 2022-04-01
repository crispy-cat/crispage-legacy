<?php
	/*
		Crispage - A lightweight CMS for developers
		core/comments/CommentManager.php - Comment manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/comments/Comment.php";

	class CommentManager {
		public function getComment(string $id = null) : ?Comment {
			if ($id == null) return null;
			global $app;

			$article = $app->database->readRow("comments", $id);
			if (!$article) return null;

			$article = new Comment($article);
			return $article;
		}

		public function setComment(string $id, Comment $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("comments", $id, array(
				"article"	=> $data->article,
				"created"	=> $data->created,
				"modified"	=> $data->modified,
				"author"	=> $data->author,
				"content"	=> $data->content
			));

			$app->events->trigger("comments.comment_set", $id);
		}

		public function deleteComment(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("comments", $id);
			$app->events->trigger("comments.comment_delete", $id);
		}

		public function existsComment(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("comments", $id);
		}
		
		public function gComments(string $article = null) : Generator {
			global $app;
			
			if ($article) $dbcomms = $app->database->readRows("comments", array("article" => $article));
			else $dbcomms = $app->database->readRows("comments");
			
			foreach ($dbcomms as $comment)
				yield new Comment($comment);
		}

		public function getComments(string $article = null) : array {
			$comments = array();

			foreach ($this->gComments($article) as $comment)
				$comments[] = $comment;

			return $comments;
		}
	}
?>
