<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/ban_user.php - Backend user ban page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["user_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("users")->exists($app->request->query["user_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("user_does_not_exist")));

	$user = $app("users")->get($app->request->query["user_id"]);

	if ($app->request->query["user_id"] == \Crispage\Assets\Session::getCurrentSession()->user) {
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => $app("i18n")->getString("target_not_current_user")));
	} else {
		if (!\Crispage\Assets\User::userHasPermissions(\Crispage\Assets\Session::getCurrentSession()->user, \Crispage\Users\UserPermissions::BAN_USERS))
			$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => $app("i18n")->getString("no_permission_bans")));

		if (\Crispage\Assets\User::compareUserRank(\Crispage\Assets\Session::getCurrentSession()->user, $app->request->query["user_id"]) !== 1)
			$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => $app("i18n")->getString("rank_less_than_own")));
	}

	if (isset($app->request->query["ban_expires"])) {
		$expires = strtotime($app->request->query["ban_expires"]);
		if (!$expires)
			$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => $app("i18n")->getString("invalid_expire_time")));

		$reason = $app->request->query["ban_reason"] ?? null;

		$id = Randomizer::randomString(16, 62);
		$ban = new \Crispage\Assets\Ban(array(
			"id"		=> $id,
			"user"		=> $user->id,
			"created"	=> time(),
			"modified"	=> time(),
			"expires"	=> $expires,
			"reason"	=> $reason
		));

		$app("bans")->set($id, $ban);
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "success", "content" => $app("i18n")->getString("user_banned")));
	}

	$app->vars["user_id"] = $user->id;

	$app->page->setTitle($app("i18n")->getString("ban_user"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("ban_user"); ?></h1>
					<p><?php echo $app->vars["user_id"]; ?></p>
					<form>
						<input type="hidden" name="user_id" value="<?php echo $app->request->query["user_id"]; ?>" />
						<label for="ban_reason"><?php $app("i18n")("ban_reason_c"); ?></label>
						<textarea class="form-control" name="ban_reason"></textarea>
						<label for="ban_expires"><?php $app("i18n")("ban_expires_c"); ?></label>
						<input type="datetime-local" class="form-control" name="ban_expires" required />
						<a class="btn btn-primary me-2 mt-3" href="<?php echo Config::WEBROOT; ?>/backend/users/list_bans?user_id=<?php echo $app->vars["user_id"]; ?>">Back</a>
						<button class="btn btn-danger mt-3" type="submit"><?php $app("i18n")("ban_user"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.delete", $app->request->query["user_id"]);

	$app->renderPage();
?>
