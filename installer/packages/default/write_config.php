<?php
	defined("CRISPAGE") or die();
	$app->installerMessage("Generating config.php");
	$config = file_get_contents(PACKAGE_DIR . "/default_config.php");
	$replace = array(
		"version" => CRISPAGE,
		"approot" => $app->request->query["iopts"]["approot"] ?? Config::APPROOT,
		"webroot" => $app->request->query["iopts"]["webroot"] ?? Config::WEBROOT,
		"password_table" => $app->request->query["iopts"]["password_table"] ?? "auth",
		"db_json_loc" => $app->request->query["iopts"]["db_json_loc"] ?? (Config::APPROOT . "/database"),
		"db_json_name" => $app->request->query["iopts"]["db_json_name"] ?? "database"
	);
	foreach ($replace as $text => $replacement) $config = preg_replace("/\{\{$text\}\}/", $replacement, $config);
	$app->installerMessage("Writing config.php");
	file_put_contents(Config::APPROOT . "/config.php", $config);
	$app->installerMessage("Wrote config.php");
?>
