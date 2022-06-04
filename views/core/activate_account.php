<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/activate_account.php - Frontend account activation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/header.php";

	$session = \Crispage\Assets\Session::getCurrentSession();
	if ($session)
		$app->redirectWithMessages("/", array("type" => "error", "content" => $app("i18n")->getString("active_session")));

	if (isset($app->request->query["user_id"]) && isset($app->request->query["token"])) {
		$app->events->trigger("frontend.view.activate_account.submit");
		$id = $app->request->query["user_id"];
		$act = $app->database->readRow("activation", $id);
		if (!$act)
			$app->redirectWithMessages("/activate_account", array("type" => "error", "content" => $app("i18n")->getString("invalid_user_id")));
		if ($app->request->query["token"] != $act["token"])
			$app->redirectWithMessages("/activate_account", array("type" => "error", "content" => $app("i18n")->getString("invalid_token")));
		$user = $app("users")->get($id);
		if (!$user)
			$app->redirectWithMessages("/activate_account", array("type" => "error", "content" => $app("i18n")->getString("user_not_exist")));

		$user->activated = 2;
		$user->modified = time();
		$app("users")->set($id, $user);
		$app->database->deleteRow("activation", $id);
		$app->redirectWithMessages("/login", array("type" => "success", "content" => $app("i18n")->getString("account_activated_login")));
	}

	$app->page->setTitle($app("i18n")->getString("activate_account"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<form method="post">
				<label for="user_id"><?php $app("i18n")("user_id_c"); ?></label>
				<input type="text" class="form-control" name="user_id" required />
				<label for="token"><?php $app("i18n")("activation_token_c"); ?></label>
				<input type="text" class="form-control" name="token" required />
				<button type="submit" class="btn btn-primary mt-3"><?php $app("i18n")("activate_account"); ?></button>
			</form>
		</div>
<?php
	});

	$app->events->trigger("frontend.view.activate_account");

	$app->renderPage();
?>
