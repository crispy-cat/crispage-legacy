<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/header.php - Installer standard header

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	if (defined("IS_INSTALLED") && IS_INSTALLED && !(defined("IS_REPAIR_PAGE") && IS_REPAIR_PAGE)) {
		$session = \Crispage\Assets\Session::getCurrentSession();
		if (!$session)
			$app->redirect(\Config::WEBROOT . "/login?ploc=/installer");

		if (!\Crispage\Assets\User::userHasPermissions($session->user, \Crispage\Users\UserPermissions::USE_INSTALLER))
			$app->redirectWithMessages("/", array("type" => "error", "content" => $app("i18n")->getString("no_permission_installer")));

		\Crispage\Assets\Session::refreshCurrentSession();
	}

	if (defined("IS_INSTALL_PAGE") && IS_INSTALL_PAGE) {
		if (file_exists(\Config::APPROOT . "/config.php"))
			$app->redirectWithMessages("/installer/install/default", array("type" => "error", "content" => "Crispage is already installed."));
	} else {
		if (!(defined("IS_INSTALL_PAGE") && IS_INSTALL_PAGE) && !(defined("IS_PACKAGE_PAGE") && IS_PACKAGE_PAGE) && !file_exists(\Config::APPROOT . "/config.php"))
			$app->redirect(\Config::WEBROOT . "/installer/install/install");
	}

	$app->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
	$app->page->metas["robots"] = array("name" => "robots", "content" => "noindex, follow");

	$app->page->scripts["getcookie"] = array("content" => "function getCookie(name) {var a=document.cookie.split(\";\");for(var i=0;i<a.length;i++){var p=a[i].split(\"=\");if(name==p[0].trim())return decodeURIComponent(p[1]);}return null;}function setCookie(name, value, exp) {var d=new Date();d.setTime(d.getTime()+(exp*86400000));var e=\"expires=\"+d.toUTCString();document.cookie=name+\"=\"+value+\"; \"+e+\"; path=/\";}");

	if (isset($app->request->cookies["msg_success"]))	$app->page->alerts["success"] =	array("class" => "success", "content" => $app->request->cookies["msg_success"]);
	if (isset($app->request->cookies["msg_info"]))		$app->page->alerts["info"] =	array("class" => "info", "content" => $app->request->cookies["msg_info"]);
	if (isset($app->request->cookies["msg_warning"]))	$app->page->alerts["warning"] =	array("class" => "warning", "content" => $app->request->cookies["msg_warning"]);
	if (isset($app->request->cookies["msg_error"]))		$app->page->alerts["error"] =	array("class" => "danger", "content" => $app->request->cookies["msg_error"]);

	$app->page->deleteCookie("msg_success");
	$app->page->deleteCookie("msg_info");
	$app->page->deleteCookie("msg_warning");
	$app->page->deleteCookie("msg_error");
?>
