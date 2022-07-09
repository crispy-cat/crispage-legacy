<?php
	define("STARTTIME", microtime(true));

	class Config {
		public const CONFIG_VERSION = "0.12.0";
		// Should not be changed unless you know what you're doing!
		public const APPROOT = __DIR__;
		// Should be changed to the request URI of this directory, e.g. if your
		// installation directory is at /srv/www/crispage and it is accessed in
		// a browser at http://localhost/crispage, it should be set to
		// "/crispage"
		public const WEBROOT = "/crispage";

		// PHP error output level, for production use set to E_ERROR | E_PARSE
		public const ERRORLVL = E_ALL;

		// PHPMailer SMTP debug level, for production use set to 0
		public const SMTP_DEBUG = 0;

		// Settings for the password_hash function
		public const PASSWORD_ALGO = PASSWORD_BCRYPT;
		public const PASSWORD_OPTIONS = array("cost" => 10);
		// The name of the table where you want to store authentication data
		// (e.g. passwords), changing this to something fairly random makes it
		// harder for attackers to obtain it
		public const PASSWORD_TABLE = "auth_0f19a3d7";

		// Database type, as of 0.12.0 options include JSONDatabase and
		// SQLiteDatabase
		public const DB_TYPE = "\Crispage\Database\JSONDatabase";
		// The directory where databases are stored, this could be anything as
		// long as it's a real path and the webserver has correct permissions
		public const DB_LOC = __DIR__ . "/database";
		// The name of the actual database file(s)
		public const DB_NAME = "crispage_20220218.db";
		public const DB_OPTIONS = array(
			"JSON_PRETTY" =>	true,
			"USERNAME" =>		null,
			"PASSWORD" =>		null,
		);
	}

	// == Do not edit below this line ==

	define("CRISPAGE", "0.12.3 alpha");

	ini_set("display_errors", "1");
	ini_set("display_startup_errors", "1");
	error_reporting(Config::ERRORLVL);
	ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE);
?>
