<?php
	/*
		Crispage - A lightweight CMS for developers
		core/runstart.php - Functions to run on application start

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.11.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	global $app;

	// Set language
	$app("i18n")->setLanguage($app->getSetting("language", "en-US"));
	
	// Set generator
	$app->page->metas["generator"] = array("name" => "generator", "content" => "Crispage " . CRISPAGE);
	
	// Register events
	$app->events->registerAction(new EventAction(array(
		"id" => "crispage.articlesetroute",
		"event" => "assets.articles.set",
		"priority" => -64,
		"action" => function($app, $id) {
			$app->database->writeRow("routes", Router::getArticleRoute($id), array("item_id" => $id, "view" => "core/article"));
		}
	)));

	$app->events->registerAction(new EventAction(array(
		"id" => "crispage.articledeleteroute",
		"event" => "assets.articles.delete.pre",
		"priority" => -64,
		"action" => function($app, $id) {
			$app->database->deleteRow("routes", Router::getArticleRoute($id));
		}
	)));

	$app->events->registerAction(new EventAction(array(
		"id" => "crispage.categorysetroute",
		"event" => "assets.categories.set",
		"priority" => -64,
		"action" => function($app, $id) {
			$app->database->writeRow("routes", Router::getCategoryRoute($id), array("item_id" => $id, "view" => "core/category"));
		}
	)));

	$app->events->registerAction(new EventAction(array(
		"id" => "crispage.categorydeleteroute",
		"event" => "assets.categories.delete.pre",
		"priority" => -64,
		"action" => function($app, $id) {
			$app->database->deleteRow("routes", Router::getCategoryRoute($id));
		}
	)));
?>
