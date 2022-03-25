<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/header.php - Installer standard header

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	if (defined("IS_INSTALLED") && IS_INSTALLED) {
		$session = $app->session->getCurrentSession();
		if (!$session)
			$app->redirect(Config::WEBROOT . "/login?ploc=/installer");

		if (!$app->users->userHasPermissions($session->user, UserPermissions::USE_INSTALLER))
			$app->redirect(Config::WEBROOT . "/?me=You do not have permission to use the installer");
	}

	if (defined("IS_INSTALL_PAGE") && IS_INSTALL_PAGE) {
		if (file_exists(Config::APPROOT . "/config.php"))
			$app->redirectWithMessages("/installer/install/default", array("type" => "error", "content" => "Crispage is already installed."));
	} else {
		if ((!defined("IS_INSTALL_PAGE")) && !file_exists(Config::APPROOT . "/config.php"))
			$app->redirect(Config::WEBROOT . "/installer/install/install");
	}

	$app->session->refreshCurrentSession();

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
