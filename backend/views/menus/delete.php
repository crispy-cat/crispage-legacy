<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menus/delete.php - Backend menu delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_MENUS))
		$app->redirect(Config::WEBROOT . "/backend/menus/list?me=You do not have permission to delete menus");

	if (!isset($app->request->query["delete_id"]))
		$app->redirect(Config::WEBROOT . "/backend/menus/list?me=No ID Specified");

	if (!$app->menus->existsMenu($app->request->query["delete_id"]))
		$app->redirect(Config::WEBROOT . "/backend/menus/list?me=Menu does not exist");

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		if (count($app->menus->getMenus()) < 2)
			$app->redirect(Config::WEBROOT . "/backend/menus/list?me=There must be at least one menu");

		$app->menus->deleteMenu($app->request->query["delete_id"]);
		$app->redirect(Config::WEBROOT . "/backend/menus/list?ms=Menu deleted.");
	}

	$app->vars["menu_title"] = htmlentities($app->menus->getMenu($app->request->query["delete_id"])->title);

	$app->page->setTitle("Delete {$app->vars["menu_title"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->vars["menu_title"]; ?>'</h1>
					<p>Are you sure you want to delete this menu? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/menus/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menus.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
