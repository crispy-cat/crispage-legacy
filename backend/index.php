<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/index.php - Backend entry script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	require_once __DIR__ . "/../config.php";

	require_once Config::APPROOT . "/backend/core/Backend.php";
	require_once Config::APPROOT . "/core/Router.php";
	$app = new Backend();
	set_exception_handler(function($e) {
		global $app;
		$app->error($e);
	});
	Router::routeRequest("/backend", "dashboard");
?>
