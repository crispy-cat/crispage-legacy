<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/media.php - Backend media manager

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.1.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$inpath = Config::APPROOT . "/media" . ($app->request->query["path"] ?? "/");
	$path = realpath($inpath) . "/";
	if (!$path || $inpath != $path) $path = Config::APPROOT . "/media/";

	$action = $app->request->query["action"] ?? null;

	if ($action == "upload") {
		if (isset($app->request->files["upload_file"])) {
			if ($app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_MEDIA)) {
				foreach ($app->request->files["upload_file"]["error"] as $file => $err) {
					if ($err == UPLOAD_ERR_OK)
						move_uploaded_file($app->request->files["upload_file"]["tmp_name"][$file], $path . basename($app->request->files["upload_file"]["name"][$file]));
					else
						$app->page->alerts["upload_error_$file"] = array("class" => "warning", "content" => $app->request->files["upload_file"]["name"][$file] . " could not be uploaded");
				}
				$app->page->alerts["upload_success"] = array("class" => "success", "content" => "Files uploaded.");
			} else {
				$app->page->alerts["user_permissions"] = array("class" => "danger", "content" => "You do not have permission to upload media");
			}
		} else {
			$app->page->alerts["upload_no_file"] = array("class" => "danger", "content" => "No file uploaded");
		}
	} elseif ($action == "delete") {
		if (isset($app->request->query["delete_name"])) {
			if ($app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_MEDIA)) {
				$dname = $path . preg_replace("/[\\/\\\\]/", "", basename($app->request->query["delete_name"]));
				if (file_exists($dname)) FileHelper::deleteRecurs($dname);
				$app->page->alerts["delete_success"] = array("class" => "success", "content" => "File deleted.");
			} else {
				$app->page->alerts["user_permissions"] = array("class" => "danger", "content" => "You do not have permission to delete media");
			}
		}
	} elseif ($action == "mkdir") {
		if (isset($app->request->query["dir_name"])) {
			if ($app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_MEDIA)) {
				$dname = $path . preg_replace("/[\\/\\\\]/", "", basename($app->request->query["dir_name"]));
				if (!file_exists($dname)) mkdir($dname);
				$app->page->alerts["mkdir_success"] = array("class" => "success", "content" => "Directory created.");
			} else {
				$app->page->alerts["user_permissions"] = array("class" => "danger", "content" => "You do not have permission to make media directories");
			}
		}
	}


	$app->vars["files"] = array(
		array(
			"path" => preg_replace("/\\/\\//", "/", dirname($app->request->query["path"] ?? "") . "/"),
			"type" => "parent",
			"name" => "Parent directory",
			"icon" => Config::WEBROOT . "/media/system/folder.png"
		)
	);

	foreach (glob($path . "*") as $fp) {
		if ($fp == $path) continue;
		if (is_dir($fp)) {
			$app->vars["files"][] = array(
				"path" => ($app->request->query["path"] ?? "/") . basename($fp) . "/",
				"type" => "directory",
				"name" => basename($fp),
				"icon" => Config::WEBROOT . "/media/system/folder.png"
			);
		} else {
			$app->vars["files"][] = array(
				"path" => ($app->request->query["path"] ?? "/"),
				"type" => "file",
				"name" => basename($fp),
				"icon" => Config::WEBROOT . "/media" . ($app->request->query["path"] ?? "/") . basename($fp)
			);
		}
	}

	$app->vars["path"] = $path;

	$app->page->setTitle("Media");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row mb-2">
				<div class="col">
					<h1>Media</h1>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-md-6">
					<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="upload" />
						<input type="hidden" name="path" value="<?php echo $app->request->query["path"] ?? "/"; ?>" />
						<div class="row">
							<div class="col-10">
								<label for="upload_file[]">Upload files:</label>
								<input type="file" class="form-control" name="upload_file[]" multiple required />
							</div>
							<div class="col-2">
								<input type="submit" class="btn btn-primary mt-4" value="Upload" style="margin-left: -15px;" />
							</div>
						</div>
					</form>
				</div>
				<div class="col-12 col-md-6">
					<form>
						<input type="hidden" name="action" value="mkdir" />
						<input type="hidden" name="path" value="<?php echo $app->request->query["path"] ?? "/"; ?>" />
						<div class="row">
							<div class="col-9">
								<label for="dir_name">Directory Name:</label>
								<input type="text" class="form-control" name="dir_name" required />
							</div>
							<div class="col-3">
								<input type="submit" class="btn btn-primary mt-4" value="Create Directory" style="margin-left: -15px;" />
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<table class="table table-striped">
						<thead>
							<tr>
								<th colspan="2">Filename</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
<?php
						foreach($app->vars["files"] as $file) {
?>
							<tr>
								<td><img src="<?php echo $file["icon"]; ?>" width="32" height="32" onerror="this.onerror=null;this.src='<?php echo Config::WEBROOT; ?>/media/system/file.png';" /></td>
								<td><a href="<?php echo Config::WEBROOT; ?>/backend/media?path=<?php echo $file["path"]; ?>"><?php echo $file["name"]; ?></a></td>
								<td>
									<?php if ($file["type"] != "parent") { ?>
										<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/media?action=delete&path=<?php echo $app->request->query["path"] ?? "/"; ?>&delete_name=<?php echo $file["name"]; ?>"><i class="bi bi-trash"></i> Delete</a>
									<?php } ?>
								</td>
							</tr>
<?php
						}
?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.dashboard");

	$app->renderPage();
?>
