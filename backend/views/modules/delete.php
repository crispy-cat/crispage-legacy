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
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_modules")));

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("modules")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => $app("i18n")->getString("module_does_not_exist")));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app("modules")->delete($app->request->query["delete_id"]);
		$app->redirectWithMessages("/backend/modules/list", array("type" => "success", "content" => $app("i18n")->getString("module_deleted")));
	}

	$app->vars["module_title"] = htmlentities($app("modules")->get($app->request->query["delete_id"])->title);

	$app->page->setTitle($app("i18n")->getString("delete_v", null, $app->vars["module_title"]));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("delete_v", null, $app->vars["module_title"]); ?></h1>
					<p><?php $app("i18n")("sure_delete_module"); ?></p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/modules/list"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-danger" type="submit"><?php $app("i18n")("delete"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.modules.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
