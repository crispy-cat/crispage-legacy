<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/script/package.php - Package installation script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.11.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	define("IS_PACKAGE_PAGE", true);
	require_once \Config::APPROOT . "/installer/header.php";

	define("UPLOAD_MESSAGES", [
		"Success",
		"Exceeded max size",
		"Exceeded form max size",
		"Partial upload",
		"No file uploaded",
		"5",
		"No temp directory",
		"Write failed",
		"PHP extension issue"
	]);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Package Installer</title>
		<style>
			* {
				box-sizing: border-box;
			}

			body {
				width: 100%;
				max-width: 800px;
				margin: 20px auto;
				font-family: monospace;
			}

			#head {
				width: 100%;
				background: #002060;
				color: #ffffff;
				border: 1px solid #002060;
			}

			#main {
				width: 100%;
				border: 1px solid #000000;
				padding: 10px;
			}

			.btn {
				background: #dddddd;
				color: #333333;
				border: 2px solid #bbbbbb;
				border-radius: 4px;
				padding: 4px 12px;
				text-decoration: none;
			}

			.btn:hover {
				background: #eef1f5;
				border-color: #cccccc;
			}
		</style>
	</head>
	<body>
		<div id="head">
			<h1>Crispage Package Installer</h1>
		</div>
		<div id="main">
<?php
	$app->installerMessage("Preparing to install package...", \Crispage\Application\InstallerApplication::IMSG_INFO);

	try {
		if (isset($app->request->files["package"])) {
			$app->installerMessage("Package upload found.", \Crispage\Application\InstallerApplication::IMSG_INFO);
			$file = $app->request->files["package"];

			if ($file["error"] != UPLOAD_ERR_OK)
				$app->installerMessage("Package upload failure: " . UPLOAD_MESSAGES[$file["error"]] . " (" . $file["error"] . ")", \Crispage\Application\InstallerApplication::IMSG_ERROR);

			define("PACKAGE", \Crispage\Helpers\Randomizer::randomString(16, 36));
			define("PACKAGE_DIR", \Config::APPROOT . "/installer/packages/uploaded/" . PACKAGE);
			define("PACKAGE_TMP", sys_get_temp_dir() . "/" . PACKAGE . "/" . basename($file["name"]));

			mkdir(sys_get_temp_dir() . "/" . PACKAGE);
			move_uploaded_file($file["tmp_name"], PACKAGE_TMP);
			if (!\Crispage\Helpers\FileHelper::uncompress(PACKAGE_TMP, PACKAGE_DIR))
				$app->installerMessage("Package could not be uncompressed. Please try a different format.", \Crispage\Application\InstallerApplication::IMSG_ERROR);
		} elseif (isset($app->request->query["name"]) && !empty($app->request->query["name"])) {
			$app->installerMessage("Package name given.", \Crispage\Application\InstallerApplication::IMSG_INFO);

			define("PACKAGE", basename($app->request->query["name"]));
			if (isset($app->request->query["uploaded"]) && $app->request->query["uploaded"])
				define("PACKAGE_DIR", \Config::APPROOT . "/installer/packages/uploaded/" . PACKAGE);
			else
				define("PACKAGE_DIR", \Config::APPROOT . "/installer/packages/" . PACKAGE);
		} else {
			$app->installerMessage("Nothing to install!", \Crispage\Application\InstallerApplication::IMSG_ERROR);
		}

		define("TMPPACK", PACKAGE_DIR);
		define("TMPEXT", PACKAGE_DIR);

		if (!file_exists(PACKAGE_DIR . "/installation_scripts/install.php"))
			$app->installerMessage("Package does not contain an installation script.", \Crispage\Application\InstallerApplication::IMSG_ERROR);

		include_once PACKAGE_DIR . "/installation_scripts/install.php";

		$app->installerMessage("The installation completed without errors.", \Crispage\Application\InstallerApplication::IMSG_SUCCESS);

	} catch (\Throwable $e) {
		echo "<br /><pre style=\"color: red;\">$e</pre><br /><a class=\"btn\" href=\"" . \Config::WEBROOT . "/installer/script/package?uploaded=1&name=" . ((defined("PACKAGE")) ? PACKAGE : "") . "\">Retry</a>";
	}
?>
			<a class="btn" href="<?php echo \Config::WEBROOT . ($app->request->query["ploc"] ?? "/installer"); ?>">Back</a>
		</div>
	</body>
</html>
