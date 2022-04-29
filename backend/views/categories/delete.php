<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/categories/delete.php - Backend category delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_CATEGORIES))
		$app->redirectWithMessages("/backend/categories/list", array("type" => "error", "content" => "You do not have permission to delete categories"));

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/categories/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app("categories")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/categories/list", array("type" => "error", "content" => "Category does not exist"));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		if (count($app("categories")->getAllArr()) < 2)
			$app->redirectWithMessages("/backend/categories/list", array("type" => "error", "content" => "There must be at least one category"));

		$app("categories")->delete($app->request->query["delete_id"]);
		$app->redirectWithMessages("/backend/categories/list", array("type" => "success", "content" => "Category deleted."));
	}

	$app->vars["category_title"] = htmlentities($app("categories")->get($app->request->query["delete_id"])->title);

	$app->page->setTitle("Delete {$app->vars["category_title"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->vars["category_title"]; ?>'</h1>
					<p>Are you sure you want to delete this category? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/categories/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.categories.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
