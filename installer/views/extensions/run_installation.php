<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/extensions/run_installation.php - Extension installation run view

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
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

	installer_message("Preparing to install extension...", IMSG_INFO);
	try {
		if (!isset($app->request->files["extension_pack"])) throw new Exception("No extension pack uploaded");
		$file = $app->request->files["extension_pack"];
		$zfile = Config::APPROOT . "/installer/files/package_tmp/upload_" . Randomizer::randomString(16, 36);
		if ($file["error"] == UPLOAD_ERR_OK) move_uploaded_file($file["tmp_name"], $zfile);
		else throw new Exception("Could not upload extension pack!");

		$zip = new ZipArchive();
		if ($zip->open($zfile)) {
			define("TMPEXT", Config::APPROOT . "/installer/files/package_tmp/extension_" . Randomizer::randomString(16, 36));
			mkdir(TMPEXT);
			$zip->extractTo(TMPEXT);
			$zip->close();
		} else {
			throw new Exception("Invalid extension pack");
		}

		require_once TMPEXT . "/installation_scripts/install.php";
		installer_message("Installation complete!", IMSG_SUCCESS);
		echo "<a href=\"" . Config::WEBROOT . "/installer/extensions/list\">Back</a>";
	} catch (Throwable $e) {
		installer_message("Installation could not be completed: $e<br /><a href=\"" . Config::WEBROOT . "/installer/extensions/list\">Back</a>", IMSG_ERROR);
	}
?>
