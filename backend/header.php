<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/header.php - Backend standard header

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	$session = Session::getCurrentSession();
	if (!$session)
		$app->redirect(Config::WEBROOT . "/login?ploc=/backend");

	if (!User::userHasPermissions($session->user, UserPermissions::LOGIN_BACKEND))
		$app->redirectWithMessages("/", array("type" => "error", "content" => "You do not have permission to use the backend"));

	Session::refreshCurrentSession();

	$app->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
	$app->page->metas["robots"] = array("name" => "robots", "content" => "noindex, follow");

	if (isset($app->request->cookies["msg_success"]))	$app->page->alerts["success"] =	array("class" => "success", "content" => $app->request->cookies["msg_success"]);
	if (isset($app->request->cookies["msg_info"]))		$app->page->alerts["info"] =	array("class" => "info", "content" => $app->request->cookies["msg_info"]);
	if (isset($app->request->cookies["msg_warning"]))	$app->page->alerts["warning"] =	array("class" => "warning", "content" => $app->request->cookies["msg_warning"]);
	if (isset($app->request->cookies["msg_error"]))		$app->page->alerts["error"] =	array("class" => "danger", "content" => $app->request->cookies["msg_error"]);

	$app->page->deleteCookie("msg_success");
	$app->page->deleteCookie("msg_info");
	$app->page->deleteCookie("msg_warning");
	$app->page->deleteCookie("msg_error");
?>
