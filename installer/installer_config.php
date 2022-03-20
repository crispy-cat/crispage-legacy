<?php
	define("APPROOT", dirname(__DIR__));
	define("WEBROOT", substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], "/installer")));

	class Config {
		public const CONFIG_VERSION = "0.2.0";
		public const APPROOT = APPROOT;
		public const WEBROOT = WEBROOT;
		public const ERRORLVL = E_ALL;
		public const SMTP_DEBUG = 2;
	}

	define("CRISPAGE", "0.4.11 alpha");

	ini_set("display_errors", "1");
	ini_set("display_startup_errors", "1");
	error_reporting(Config::ERRORLVL);
?>
