<?php
	defined("CRISPAGE") or die();
	$app->installerMessage("Generating config.php");
	$config = file_get_contents(PACKAGE_DIR . "/default_config.php");
	$replace = array(
		"version" => CRISPAGE,
		"approot" => $app->request->query["iopts"]["approot"] ?? \Config::APPROOT,
		"webroot" => $app->request->query["iopts"]["webroot"] ?? \Config::WEBROOT,
		"password_table" => $app->request->query["iopts"]["password_table"] ?? "auth",
		"db_loc" => $app->request->query["iopts"]["db_loc"] ?? (\Config::APPROOT . "/database"),
		"db_name" => $app->request->query["iopts"]["db_name"] ?? "database",
		"db_type" => $app->request->query["iopts"]["db_type"] ?? "JSONDatabase",
		"db_user" => $app->request->query["iopts"]["db_user"] ?? "",
		"db_pass" => $app->request->query["iopts"]["db_pass"] ?? "",
		"db_json_pretty" => $app->request->query["iopts"]["db_json_pretty"] ?? "false",
	);
	foreach ($replace as $text => $replacement) $config = preg_replace("/\{\{$text\}\}/", $replacement, $config);
	$app->installerMessage("Writing config.php");
	file_put_contents(\Config::APPROOT . "/config.php", $config);
	$app->installerMessage("Wrote config.php");
?>
