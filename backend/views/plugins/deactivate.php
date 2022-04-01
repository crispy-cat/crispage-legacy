<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/plugins/deactivate.php - Backend plugin deactivation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.5.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_PLUGINS))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "You do not have permission to deactivate plugins"));

	if (!isset($app->request->query["deactivate_id"]))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app->extensions->existsplugin($app->request->query["deactivate_id"]))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "Plugin does not exist"));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app->extensions->deleteplugin($app->request->query["deactivate_id"]);
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "success", "content" => "Plugin deactivated"));
	}

	$app->page->setTitle("Deactivate Plugin");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->request->query["deactivate_id"]; ?>'</h1>
					<p>Are you sure you want to deactivate this plugin? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="deactivate_id" value="<?php echo $app->request->query["deactivate_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/plugins/list">Back</a>
						<button class="btn btn-danger" type="submit">Deactivate</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.plugins.delete", $app->request->query["deactivate_id"]);

	$app->renderPage();
?>
