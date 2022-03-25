<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/activate_account.php - Frontend account activation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$session = $app->session->getCurrentSession();
	if ($session)
		$app->redirect(Config::WEBROOT . "/?me=There is an active session");

	if (isset($app->request->query["user_id"]) && isset($app->request->query["token"])) {
		$id = $app->request->query["user_id"];
		$act = $app->database->readRow("activation", $id);
		if (!$act)
			$app->redirect(Config::WEBROOT . "/activate_account?me=Invalid user ID");
		if ($app->request->query["token"] != $act["token"])
			$app->redirect(Config::WEBROOT . "/activate_account?me=Invalid token");
		$user = $app->users->getUser($id);
		if (!$user)
			$app->redirect(Config::WEBROOT . "/activate_account?me=User does not exist");

		$user->activated = 2;
		$user->modified = time();
		$app->users->setUser($id, $user);
		$app->database->deleteRow("activation", $id);
		$app->redirect(Config::WEBROOT . "/login?ms=Account activated. Please log in.");
	}

	$app->page->setTitle("Activate Account");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form method="post">
				<label for="user_id">User ID:</label>
				<input type="text" class="form-control" name="user_id" required />
				<label for="token">Activation Token:</label>
				<input type="text" class="form-control" name="token" required />
				<button type="submit" class="btn btn-primary mt-3">Activate</button>
			</form>
		</div>
<?php
	});

	$app->renderPage();
?>
