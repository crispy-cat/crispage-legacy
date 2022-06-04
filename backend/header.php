<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/header.php - Backend standard header

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	$session = \Crispage\Assets\Session::getCurrentSession();
	if (!$session)
		$app->redirect(Config::WEBROOT . "/login?ploc=/backend");

	if (!\Crispage\Assets\User::userHasPermissions($session->user, Crispage\Users\UserPermissions::LOGIN_BACKEND))
		$app->redirectWithMessages("/", array("type" => "error", "content" => $app("i18n")->getString("no_permission_backend")));

	\Crispage\Assets\Session::refreshCurrentSession();

	$app->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
	$app->page->metas["robots"] = array("name" => "robots", "content" => "noindex, follow");

	$app->page->scripts["getcookie"] = array("content" => "function getCookie(name) {var a=document.cookie.split(\";\");for(var i=0;i<a.length;i++){var p=a[i].split(\"=\");if(name==p[0].trim())return decodeURIComponent(p[1]);}return null;}function setCookie(name, value, exp) {var d=new Date();d.setTime(d.getTime()+(exp*86400000));var e=\"expires=\"+d.toUTCString();document.cookie=name+\"=\"+value+\"; \"+e+\"; path=/\";}");
	$app->page->scripts["ckeditor"] = array("src" => \Config::WEBROOT . "/media/system/js/ckeditor.js", "defer" => true);
	$app->page->scripts["editor"] = array("src" => \Config::WEBROOT . "/media/system/js/editor.js", "defer" => true);

	if (isset($app->request->cookies["msg_success"]))	$app->page->alerts["success"] =	array("class" => "success", "content" => $app->request->cookies["msg_success"]);
	if (isset($app->request->cookies["msg_info"]))		$app->page->alerts["info"] =	array("class" => "info", "content" => $app->request->cookies["msg_info"]);
	if (isset($app->request->cookies["msg_warning"]))	$app->page->alerts["warning"] =	array("class" => "warning", "content" => $app->request->cookies["msg_warning"]);
	if (isset($app->request->cookies["msg_error"]))		$app->page->alerts["error"] =	array("class" => "danger", "content" => $app->request->cookies["msg_error"]);

	$app->page->deleteCookie("msg_success");
	$app->page->deleteCookie("msg_info");
	$app->page->deleteCookie("msg_warning");
	$app->page->deleteCookie("msg_error");
?>
