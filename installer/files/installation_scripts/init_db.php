<?php
	defined("CRISPAGE") or die();

	$approot = $app->request->query["approot"] ?? Config::APPROOT;
	$webroot = $app->request->query["webroot"] ?? Config::WEBROOT;
	$dbloc = $app->request->query["db_json_loc"] ?? ($approot . "/database");
	$dbname = $app->request->query["db_json_name"] ?? "database";
	$passtable = $app->request->query["password_table"] ?? "auth";

	installer_message("Initializing database");
	$app->initDatabase("JSONDatabase", array(
		"location" => $dbloc,
		"name" => $dbname,
		"pretty" => false
	));

	installer_message("Creating database");
	mkdir($dbloc . "/" . $dbname);

	installer_message("Creating tables");
	$app->database->createTable("activation", array(
		"id" => "string",
		"token" => "string"
	));
	$app->database->createTable("articles", array(
		"id" => "string",
		"category" => "string",
		"state" => "string",
		"created" => "integer",
		"modified" => "integer",
		"author" => "string",
		"summary" => "string",
		"tags" => "string",
		"title" => "string",
		"content" => "string",
		"meta_desc" => "string",
		"meta_keys" => "string",
		"meta_robots" => "string",
		"hits" => "integer",
		"options" => "array"
	));
	$app->database->createTable("authreset", array(
		"id" => "string",
		"token" => "string"
	));
	$app->database->createTable("bans", array(
		"id" => "string",
		"user" => "string",
		"created" => "integer",
		"modified" => "integer",
		"expires" => "integer",
		"reason" => "string",
		"options" => "array"
	));
	$app->database->createTable("categories", array(
		"id" => "string",
		"parent" => "string",
		"state" => "string",
		"created" => "integer",
		"modified" => "integer",
		"title" => "string",
		"content" => "string",
		"tags" => "string",
		"meta_desc" => "string",
		"meta_keys" => "string",
		"meta_robots" => "string",
		"options" => "array"
	));
	$app->database->createTable("comments", array(
		"id" => "string",
		"article" => "string",
		"created" => "integer",
		"modified" => "integer",
		"author" => "string",
		"content" => "string",
		"options" => "array"
	));
	$app->database->createTable("installation", array(
		"id" => "string",
		"type" => "string",
		"class" => "string",
		"scope" => "string"
	));
	$app->database->createTable("menuitems", array(
		"id" => "string",
		"label" => "string",
		"type" => "string",
		"menu" => "string",
		"parent" => "string",
		"ord" => "integer",
		"content" => "string",
		"created" => "integer",
		"modified" => "integer"
	));
	$app->database->createTable("menus", array(
		"id" => "string",
		"title" => "string",
		"created" => "integer",
		"modified" => "integer",
		"options" => "array"
	));
	$app->database->createTable("modules", array(
		"id" => "string",
		"title" => "string",
		"class" => "string",
		"pos" => "string",
		"ord" => "integer",
		"scope" => "string",
		"created" => "integer",
		"modified" => "integer",
		"options" => "array"
	));
	$app->database->createTable("plugins", array(
		"id" => "string",
		"class" => "string",
		"priority" => "integer",
		"scope" => "string",
		"created" => "integer",
		"modified" => "integer",
		"options" => "array"
	));
	$app->database->createTable("routes", array(
		"id" => "string",
		"item_id" => "string",
		"view" => "string"
	));
	$app->database->createTable("sessions", array(
		"id" => "string",
		"user" => "string",
		"ip" => "string",
		"lastactive" => "integer",
		"created" => "integer",
		"modified" => "integer",
		"options" => "integer"
	));
	$app->database->createTable("settings", array(
		"id" => "string",
		"value" => "string"
	));
	$app->database->createTable("usergroups", array(
		"id" => "string",
		"name" => "string",
		"parent" => "string",
		"rank" => "integer",
		"permissions" => "integer",
		"created" => "integer",
		"modified" => "integer",
		"options" => "array"
	));
	$app->database->createTable("users", array(
		"id" => "string",
		"name" => "string",
		"email" => "string",
		"group" => "string",
		"created" => "integer",
		"modified" => "integer",
		"loggedin" => "integer",
		"activated" => "integer",
		"options" => "array"
	));
	$app->database->createTable($passtable, array(
		"id" => "string",
		"password" => "string"
	));

	installer_message("Populating tables");
	$app->database->writeRow("categories", "uncategorized", array(
		"parent" => "",
		"state" => "published",
		"created" => time(),
		"modified" => time(),
		"title" => "Uncategorized",
		"content" => "",
		"tags" => "",
		"meta_desc" => "",
		"meta_keys" => "",
		"meta_robots" => "",
		"options" => array()
	));
	$app->database->writeRow("usergroups", "super-user", array(
		"name" => "Super User",
		"parent" => "",
		"rank" => -1,
		"permissions" => UserPermissions::ALL_PERMISSIONS,
		"created" => time(),
		"modified" => time(),
		"options" => array()
	));
	$app->database->writeRow("users", $app->request->query["super_user_id"] ?? "super-user", array(
		"name" => $app->request->query["super_user_name"] ?? "Super User",
		"email" => $app->request->query["super_user_email"] ?? "",
		"group" => "super-user",
		"created" => time(),
		"modified" => time(),
		"loggedin" => 0,
		"activated" => time(),
		"options" => array()
	));
	$app->database->writeRow($passtable, $app->request->query["super_user_id"] ?? "super-user", array(
		"password" => password_hash($app->request->query["super_user_password"] ?? "password", PASSWORD_BCRYPT, array("cost" => 10))
	));
	$app->database->writeRow("settings", "sitename", array("value" => $app->request->query["sitename"] ?? ""));
	$app->database->writeRow("settings", "site_desc", array("value" => $app->request->query["sitedesc"] ?? ""));
	$app->database->writeRow("settings", "charset", array("value" => $app->request->query["charset"] ?? "UTF-8"));
	$app->database->writeRow("settings", "timezone", array("value" => $app->request->query["timezone"] ?? "America/New_York"));
	$app->database->writeRow("settings", "date_format", array("value" => $app->request->query["date_format"] ?? "Y-m-d"));
	$app->database->writeRow("settings", "time_format", array("value" => $app->request->query["time_format"] ?? "H:i"));
	$app->database->writeRow("settings", "date_format_long", array("value" => $app->request->query["date_format_long"] ?? "Y, F j"));
	$app->database->writeRow("settings", "timee_format_long", array("value" => $app->request->query["time_format_long"] ?? "H:i:s"));
	$app->database->writeRow("settings", "template", array("value" => "crispy"));
	$app->database->writeRow("settings", "backend_template", array("value" => "crispage"));
	$i = 0;
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/activate_account"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/article"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/category"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/login"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/logout"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/post_comment"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/register"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/reset_password"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/search"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "view", "class" => "core/user_profile"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "template", "class" => "installer/template"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "template", "class" => "crispage/template"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "template", "class" => "crispy/template"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/breadcrumbs/BreadcrumbModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/comments/CommentsModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/content/LatestArticlesModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/content/PopularArticlesModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/menu/NavMenuModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/search/SearchBoxModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/text/CustomModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "module", "class" => "core/user/LoginModule"));
	$app->database->writeRow("installation", $i++, array("scope" => "frontend", "type" => "plugin", "class" => "core/example/ExamplePlugin"));

	$app->database->writeRow("routes", "index", array("view" => "core/article", "item_id" => "index"));
	$app->database->writeRow("routes", "search", array("view" => "core/search", "item_id" => ""));
	$app->database->writeRow("routes", "login", array("view" => "core/login", "item_id" => ""));
	$app->database->writeRow("routes", "logout", array("view" => "core/logout", "item_id" => ""));
	$app->database->writeRow("routes", "register", array("view" => "core/register", "item_id" => ""));
	$app->database->writeRow("routes", "user_profile", array("view" => "core/user_profile", "item_id" => ""));
	$app->database->writeRow("routes", "acivate_account", array("view" => "core/activate_account", "item_id" => ""));
	$app->database->writeRow("routes", "reset_password", array("view" => "core/reset_password", "item_id" => ""));
	$app->database->writeRow("routes", "post_comment", array("view" => "core/post_comment", "item_id" => ""));

	$app->database->writeChanges();

	installer_message("Wrote database files")
?>
