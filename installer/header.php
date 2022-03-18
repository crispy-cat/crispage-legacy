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
			$app->redirect(Config::WEBROOT . "/installer/install/default?me=Crispage is already installed.");
	} else {
		if ((!defined("IS_INSTALL_PAGE")) && !file_exists(Config::APPROOT . "/config.php"))
			$app->redirect(Config::WEBROOT . "/installer/install/install");
	}

	$app->session->refreshCurrentSession();

	$app->page->metas["charset"] = array("charset" => $this->getSetting("charset", "UTF-8"));
	$app->page->metas["robots"] = array("name" => "robots", "content" => "noindex, follow");

	if (isset($app->request->query["ms"]))	$app->page->alerts["success"] =	array("class" => "success", "content" => $app->request->query["ms"]);
	if (isset($app->request->query["mi"]))	$app->page->alerts["info"] =	array("class" => "info", "content" => $app->request->query["mi"]);
	if (isset($app->request->query["mw"]))	$app->page->alerts["warning"] =	array("class" => "warning", "content" => $app->request->query["mw"]);
	if (isset($app->request->query["me"]))	$app->page->alerts["error"] =	array("class" => "danger", "content" => $app->request->query["me"]);
?>
