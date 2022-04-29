<?php
	/*
		Crispage - A lightweight CMS for developers
		core/events/defaultevents.php - Default appliication events

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	$this->events->registerAction(new EventAction(array(
		"id" => "crispage.articlesetroute",
		"event" => "assets.articles.set",
		"priority" => -16,
		"action" => function($app, $id) {
			$app->database->writeRow("routes", Router::getArticleRoute($id), array("item_id" => $id, "view" => "core/article"));
		}
	)));

	$this->events->registerAction(new EventAction(array(
		"id" => "crispage.articledeleteroute",
		"event" => "assets.articles.delete.pre",
		"priority" => -16,
		"action" => function($app, $id) {
			$app->database->deleteRow("routes", Router::getArticleRoute($id));
		}
	)));

	$this->events->registerAction(new EventAction(array(
		"id" => "crispage.categorysetroute",
		"event" => "assets.categories.set",
		"priority" => -16,
		"action" => function($app, $id) {
			$app->database->writeRow("routes", Router::getCategoryRoute($id), array("item_id" => $id, "view" => "core/category"));
		}
	)));

	$this->events->registerAction(new EventAction(array(
		"id" => "crispage.categorydeleteroute",
		"event" => "assets.categories.delete.pre",
		"priority" => -16,
		"action" => function($app, $id) {
			$app->database->deleteRow("routes", Router::getCategoryRoute($id));
		}
	)));
?>
