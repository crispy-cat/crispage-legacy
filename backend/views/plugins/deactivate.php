<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/plugins/deactivate.php - Backend plugin deactivation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.5.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	if (!\Crispage\Assets\User::userHasPermissions(\Crispage\Assets\Session::getCurrentSession()->user, \Crispage\Users\UserPermissions::MODIFY_PLUGINS))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_plugins")));

	if (!isset($app->request->query["deactivate_id"]))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("plugins")->exists($app->request->query["deactivate_id"]))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("plugin_does_not_exist")));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app("plugins")->delete($app->request->query["deactivate_id"]);
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "success", "content" => $app("i18n")->getString("plugin_deactivated")));
	}

	$app->vars["deactivate_id"] = $app->request->query["deactivate_id"];

	$app->page->setTitle($app("i18n")->getString("delete_v", null, $app->vars["deactivate_id"]));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("deactivate_v", null, $app->vars["deactivate_id"]); ?></h1>
					<p><?php $app("i18n")("sure_delete_plugin"); ?></p>
					<form class="d-flex">
						<input type="hidden" name="deactivate_id" value="<?php echo $app->request->query["deactivate_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/plugins/list"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-danger" type="submit"><?php $app("i18n")("deactivate"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.plugins.delete", $app->request->query["deactivate_id"]);

	$app->renderPage();
?>
