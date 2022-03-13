<?php
	/*
		Crispage - A lightweight CMS for developers
		index.php - Entry script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	require_once __DIR__ . "/config.php";

	require_once Config::APPROOT . "/core/Application.php";
	include_once Config::APPROOT . "/core/Router.php";
	$app = new Application();
	Router::routeRequest();
?>
