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

	$session = Session::getCurrentSession();
	if (!$session)
		$app->redirectWithMessages($ploc, array("type" => "error", "content" => "There is no active session"));

	Session::endCurrentSession();
	$app->events->trigger("users.log_out", $session->user);

	$app->redirectWithMessages($ploc, array("type" => "success", "content" => "Logged out."));
?>
