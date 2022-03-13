<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/logout.php - Frontend logout page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$ploc = $app->request->query["ploc"] ?? "";

	$session = $app->session->getCurrentSession();
	if (!$session)
		$app->redirect(Config::WEBROOT . "/$ploc?me=There is no active session");

	$app->session->endCurrentSession();
	$app->events->trigger("users.log_out", $session->user);

	$app->redirect(Config::WEBROOT . "/$ploc?ms=Logged out.");
?>
