<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/index.php - Backend entry script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	require_once __DIR__ . "/../config.php";

	define("CRISPAGE", "0.2.0 alpha");

	ini_set("display_errors", "1");
	ini_set("display_startup_errors", "1");
	error_reporting(Config::ERRORLVL);

	require_once Config::APPROOT . "/backend/core/Backend.php";
	include_once Config::APPROOT . "/core/Router.php";
	$app = new Backend();
	Router::routeRequest("/backend", "dashboard");
?>
