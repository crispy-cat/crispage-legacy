<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/files/installation_scripts/install.php - Main installation script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.2
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	
	require_once __DIR__ . "/write_config.php";
	require_once __DIR__ . "/init_db.php";
?>
