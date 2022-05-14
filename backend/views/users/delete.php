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
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("users")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("user_does_not_exist")));

	$user = $app("users")->get($app->request->query["delete_id"]);

	if ($user->id == Session::getCurrentSession()->user) {
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("target_not_current_user")));
	} else {
		if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_USERS))
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_users")));

		if (User::compareUserRank(Session::getCurrentSession()->user, $app->request->query["delete_id"]) !== 1)
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("rank_less_than_own")));
	}

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		if (count($app("users")->getAllArr()) < 2)
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("must_be_one_user")));

		$app("users")->delete($user->id);
		$app->redirectWithMessages("/backend/users/list", array("type" => "success", "content" => $app("i18n")->getString("user_deleted")));
	}

	$app->vars["user_name"] = htmlentities($user->name);

	$app->page->setTitle($app("i18n")->getString("delete_v", null, $app->vars["user_name"]));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("delete_v", null, $app->vars["user_name"]); ?></h1>
					<p><?php $app("i18n")("sure_delete_user"); ?></p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/users/list"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-danger" type="submit"><?php $app("i18n")("delete"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
