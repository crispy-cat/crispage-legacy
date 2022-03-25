<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/reset_password.php - Backend password reset page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["reset_id"]))
		$app->redirect(Config::WEBROOT . "/backend/users/list?me=No ID Specified");

	if (!$app->users->existsUser($app->request->query["reset_id"]))
		$app->redirect(Config::WEBROOT . "/backend/users/list?me=User does not exist");

	if ($app->request->query["reset_id"] == $app->session->getCurrentSession()->user) {
		if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_SELF))
			$app->redirect(Config::WEBROOT . "/backend/users/list?me=You do not have permission to modify yourself");
	} else {
		if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_USERS))
			$app->redirect(Config::WEBROOT . "/backend/users/list?me=You do not have permission to modify other users");
	}

	if (isset($app->request->query["user_password"])) {
		$app->auth->setPassword($app->request->query["reset_id"], $app->request->query["user_password"]);
		$app->redirect(Config::WEBROOT . "/backend/users/list?ms=Password reset.");
	}

	$app->vars["user_name"] = $app->users->getUser($app->request->query["reset_id"])->name;

	$app->page->setTitle("Reset password for {$app->vars["user_name"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Reset password for '<?php echo $app->vars["user_name"]; ?>'</h1>
					<p>Please enter a new password.</p>
					<form>
						<input type="hidden" name="reset_id" value="<?php echo $app->request->query["reset_id"]; ?>" />
						<label for="user_password">New Password:</label>
						<input type="password" class="form-control" name="user_password" />
						<a class="btn btn-secondary me-2 mt-3" href="<?php echo Config::WEBROOT; ?>/backend/users/list">Back</a>
						<button class="btn btn-success mt-3" type="submit">Reset</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.reset_password", $app->request->query["reset_id"]);

	$app->renderPage();
?>
