<?php
	/*
		Crispage - A lightweight CMS for developers
		index.php - Entry script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	if (file_exists(__DIR__ . "/config.php")) {
		require_once __DIR__ . "/config.php";
	} else {
		header("Location: http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "/installer");
		die();
	}

	require_once \Config::APPROOT . "/core/application/FrontendApplication.php";
	$app = new \Crispage\Application\FrontendApplication();
	set_exception_handler(function($e) {
		global $app;
		$app->error($e);
	});
	$app->start();
?>
