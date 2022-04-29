<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/modules/delete.php - Backend module delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_MODULES))
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => "You do not have permission to delete modules"));

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app("modules")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => "Module does not exist"));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app("modules")->delete($app->request->query["delete_id"]);
		$app->redirectWithMessages("/backend/modules/list", array("type" => "success", "content" => "Module deleted."));
	}

	$app->vars["module_title"] = htmlentities($app("modules")->get($app->request->query["delete_id"])->title);

	$app->page->setTitle("Delete {$app->vars["module_title"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->vars["module_title"]; ?>'</h1>
					<p>Are you sure you want to delete this module? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/modules/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.modules.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
