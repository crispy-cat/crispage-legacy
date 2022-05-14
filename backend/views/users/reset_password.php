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
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("users")->exists($app->request->query["reset_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("user_does_not_exist")));

	if ($app->request->query["reset_id"] == Session::getCurrentSession()->user) {
		if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_SELF))
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_self")));
	} else {
		if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_USERS))
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_users")));

		if (User::compareUserRank(Session::getCurrentSession()->user, $app->request->query["reset_id"]) !== 1)
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("rank_less_than_own")));
	}

	if (isset($app->request->query["user_password"])) {
		$app->auth->setPassword($app->request->query["reset_id"], $app->request->query["user_password"]);
		$app->redirectWithMessages("/backend/users/list", array("type" => "success", "content" => $app("i18n")->getString("password_reset")));
	}

	$app->page->setTitle($app("i18n")->getString("reset_password"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("reset_password"); ?></h1>
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
