<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/header.php - Backend standard header

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	$session = $app->session->getCurrentSession();
	if (!$session)
		$app->redirect(Config::WEBROOT . "/login?ploc=/backend");

	if (!$app->users->userHasPermissions($session->user, UserPermissions::LOGIN_BACKEND))
		$app->redirect(Config::WEBROOT . "/?me=You do not have permission to use the backend");

	$app->session->refreshCurrentSession();

	$app->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
	$app->page->metas["robots"] = array("name" => "robots", "content" => "noindex, follow");

	if (isset($app->request->query["ms"]))	$app->page->alerts["success"] =	array("class" => "success", "content" => $app->request->query["ms"]);
	if (isset($app->request->query["mi"]))	$app->page->alerts["info"] =	array("class" => "info", "content" => $app->request->query["mi"]);
	if (isset($app->request->query["mw"]))	$app->page->alerts["warning"] =	array("class" => "warning", "content" => $app->request->query["mw"]);
	if (isset($app->request->query["me"]))	$app->page->alerts["error"] =	array("class" => "danger", "content" => $app->request->query["me"]);
?>