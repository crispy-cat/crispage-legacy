<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/logout.php - Frontend logout page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/header.php";

	$ploc = preg_replace("/\/\//", "/", "/" . ($app->request->query["ploc"] ?? "/"));

	$app->events->trigger("frontend.view.logout.submit");

	$session = Session::getCurrentSession();
	if (!$session)
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => $app("i18n")->getString("no_active_session")));

	Session::endCurrentSession();
	$app->events->trigger("users.log_out", $session->user);

	$app->redirectWithMessages($ploc, array("type" => "success", "content" => $app("i18n")->getString("logged_out")));
?>
