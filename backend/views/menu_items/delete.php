<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menu_items - Backend menu item delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_MENUS))
		$app->redirectWithMessages("/backend/menu_items/list", array("type" => "error", "content" => "You do not have permission to delete menu items"));

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/menu_items/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app->menus->existsMenuItem($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/menu_items/list", array("type" => "error", "content" => "Menu does not exist"));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app->menus->deleteMenuItem($app->request->query["delete_id"]);
		$app->redirectWithMessages("/backend/menu_items/list", array("type" => "success", "content" => "Menu deleted."));
	}

	$app->vars["item_label"] = htmlentities($app->menus->getMenuItem($app->request->query["delete_id"])->label);

	$app->page->setTitle("Delete {$app->vars["item_label"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->vars["item_label"]; ?>'</h1>
					<p>Are you sure you want to delete this menu item? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menu_items.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
