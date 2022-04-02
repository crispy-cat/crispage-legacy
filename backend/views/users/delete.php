<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/delete.php - Backend user delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app->users->existsUser($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "User does not exist"));

	$user = $app->users->getUser($app->request->query["delete_id"]);

	if ($user->id == $app->session->getCurrentSession()->user) {
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "The active user cannot be deleted"));
	} else {
		if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_USERS))
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "You do not have permission to delete other users"));

		if ($app->users->compareUserRank($app->session->getCurrentSession()->user, $app->request->query["delete_id"]) !== 1)
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "Target user's group rank must be less than your own"));
	}

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		if (count($app->users->getUsers()) < 2)
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "There must be at least one user"));

		$app->users->deleteUser($user->id);
		$app->redirectWithMessages("/backend/users/list", array("type" => "success", "content" => "User deleted."));
	}

	$app->vars["user_name"] = htmlentities($user->name);

	$app->page->setTitle("Delete {$app->vars["user_name"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->vars["user_name"]; ?>'</h1>
					<p>Are you sure you want to delete this user? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/users/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
