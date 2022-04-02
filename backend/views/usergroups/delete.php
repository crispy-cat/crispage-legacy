<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/usergroups/delete.php - Backend user group delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_USERGROUPS))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => "You do not have permission to delete usergroups"));

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app->users->existsUserGroup($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => "Group does not exist"));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		if ($app->users->compareUserRank($app->session->getCurrentSession()->user, $app->users->getGroupRank($app->request->query["delete_id"])) !== 1)
			$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => "Group rank must be less than your own"));

		if (count($app->users->getUserGroups()) < 2)
			$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => "There must be at least one usergroup"));

		$app->users->deleteUserGroup($app->request->query["delete_id"]);
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "success", "content" => "Group deleted."));
	}

	$app->vars["usergroup_name"] = htmlentities($app->users->getUserGroup($app->request->query["delete_id"])->name);

	$app->page->setTitle("Delete {$app->vars["usergroup_name"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->vars["usergroup_name"]; ?>'</h1>
					<p>Are you sure you want to delete this group? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.usergroups.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
