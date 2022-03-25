<?php
	/*
		Crispage - A lightweight CMS for developers
		core/header.php - Standard header

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/
	
	defined("CRISPAGE") or die("Application must be started from index.php!");

	$app->session->refreshCurrentSession();

	$app->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
	$app->page->metas["robots"] = array("name" => "robots", "content" => $this->getSetting("meta_robots", "index, follow"));

	if (isset($app->request->query["ms"]))	$app->page->alerts["success"] =	array("class" => "success", "content" => $app->request->query["ms"]);
	if (isset($app->request->query["mi"]))	$app->page->alerts["info"] =	array("class" => "info", "content" => $app->request->query["mi"]);
	if (isset($app->request->query["mw"]))	$app->page->alerts["warning"] =	array("class" => "warning", "content" => $app->request->query["mw"]);
	if (isset($app->request->query["me"]))	$app->page->alerts["error"] =	array("class" => "danger", "content" => $app->request->query["me"]);
?>
