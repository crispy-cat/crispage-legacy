<?php
	/*
		Crispage - A lightweight CMS for developers
		core/content/ContentManager.php - Content manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/content/Article.php";
	require_once Config::APPROOT . "/core/content/Category.php";
	require_once Config::APPROOT . "/core/Router.php";

	class ContentManager {
		public function getArticle(string $id = null) : ?Article {
			if ($id == null) return null;
			global $app;

			$article = $app->database->readRow("articles", $id);
			if (!$article) return null;

			$article = new Article($article);
			return $article;
		}

		public function getCategory(string $id = null) : ?Category {
			if ($id == null) return null;
			global $app;

			$category = $app->database->readRow("categories", $id);
			if (!$category) return null;

			$category = new Category($category);
			return $category;
		}

		public function setArticle(string $id, Article $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("articles", $id, array(
				"category"	=> $data->category,
				"state"		=> $data->state,
				"created"	=> $data->created,
				"modified"	=> $data->modified,
				"author"	=> $data->author,
				"summary"	=> $data->summary,
				"tags"		=> $data->tags,
				"title"		=> $data->title,
				"content"	=> $data->content,
				"meta_desc" => $data->meta_desc,
				"meta_keys" => $data->meta_keys,
				"meta_robots" => $data->meta_robots,
				"hits"		=> $data->hits,
				"options"	=> $data->options
			));

			$app->database->writeRow("routes", Router::getArticleRoute($id), array("item_id" => $id, "view" => "core/article"));
			$app->events->trigger("content.article_set", $id);
		}

		public function setCategory(string $id, Category $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("categories", $id, array(
				"parent" 	=> $data->parent,
				"state"		=> $data->state,
				"created"	=> $data->created,
				"modified"	=> $data->modified,
				"tags"		=> $data->tags,
				"title"		=> $data->title,
				"content"	=> $data->content,
				"meta_desc" => $data->meta_desc,
				"meta_keys" => $data->meta_keys,
				"meta_robots" => $data->meta_robots,
				"options"	=> $data->options
			));

			if (!$this->categoryParentLoop($id)) $app->database->writeRow("routes", Router::getCategoryRoute($id), array("item_id" => $id, "view" => "core/category"));
			$app->events->trigger("content.category_set", $id);
		}

		public function deleteArticle(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("routes", Router::getArticleRoute($id));
			$app->database->deleteRow("articles", $id);
			$app->events->trigger("content.article_delete", $id);
		}

		public function deleteCategory(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("routes", Router::getCategoryRoute($id));
			$app->database->deleteRow("categories", $id);
			$app->events->trigger("content.category_delete", $id);
		}

		public function existsArticle(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("articles", $id);
		}

		public function existsCategory(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("categories", $id);
		}

		public function getArticles(string $category = null) : array {
			global $app;

			if ($category) $dbarts = $app->database->readRows("articles", array("category" => $category));
			else $dbarts = $app->database->readRows("articles");

			$articles = array();

			foreach ($dbarts as $article)
				array_push($articles, $this->getArticle($article["id"]));

			return $articles;
		}

		public function getCategories() : array {
			global $app;

			$dbcats = $app->database->readRows("categories");

			$categories = array();

			foreach ($dbcats as $category)
				array_push($categories, $this->getCategory($category["id"]));

			return $categories;
		}

		public function categoryParentLoop(string $id = null) : bool {
			if ($id == null) return false;

			$names = array();

			$parent = $this->getCategory($id)->parent;
			if ($parent == null) return false;

			while ($parent !== null) {
				if (in_array($parent, $names)) return true;
				array_push($names, $parent);
				$category = $this->getCategory($parent);
				if (!$category) return false;
				$parent = $category->parent;
			}

			return false;
		}
	}
?>
