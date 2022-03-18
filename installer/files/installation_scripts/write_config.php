<?php
	defined("CRISPAGE") or die();
	installer_message("Generating config.php");
	$config = file_get_contents(Config::APPROOT . "/installer/files/default_config.php");
	$replace = array(
		"approot" => $app->request->query["approot"] ?? Config::APPROOT,
		"webroot" => $app->request->query["webroot"] ?? Config::WEBROOT,
		"password_table" => $app->request->query["password_table"] ?? "auth",
		"db_json_loc" => $app->request->query["db_json_loc"] ?? (Config::APPROOT . "/database"),
		"db_json_name" => $app->request->query["db_json_name"] ?? "database"
	);
	foreach ($replace as $text => $replacement) $config = preg_replace("/\{\{$text\}\}/", $replacement, $config);
	installer_message("Writing config.php");
	file_put_contents(Config::APPROOT . "/config.php", $config);
	installer_message("Wrote config.php");
?>