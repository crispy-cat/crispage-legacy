<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/install/run_installation.php - Installer run view

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.2
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	define("IS_INSTALL_PAGE", true);
	require_once Config::APPROOT . "/installer/header.php";
	
	define("IMSG_SUCCESS", -2);
	define("IMSG_INFO", -1);
	define("IMSG_NORMAL", 0);
	define("IMSG_WARNING", 1);
	define("IMSG_ERROR", 2);
	
	function installer_message(string $msg, int $type = IMSG_NORMAL) {
		switch ($type) {
			case IMSG_SUCCESS:
				$color = "#008800";
				$el = "b";
				break;
			case IMSG_INFO:
				$color = "#3300ff";
				$el = "i";
				break;
			case IMSG_WARNING:
				$color = "#ff8800";
				$el = "b";
				break;
			case IMSG_ERROR:
				$color = "#ff0000";
				$el = "b";
				break;
			default:
				$color = "#000000";
				$el = "span";
		}
		echo "<$el style=\"color: $color;\">$msg</$el><br />";
		if ($type == IMSG_ERROR) die();
	}
	
	installer_message("Preparing to install...", IMSG_INFO);
	try {
		require_once Config::APPROOT . "/installer/files/installation_scripts/install.php";
		installer_message("Installation complete!", IMSG_SUCCESS);
	} catch (Throwable $e) {
		installer_message("Installation could not be completed: $e", IMSG_ERROR);
	}
?>
