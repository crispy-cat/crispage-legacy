<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/unban_user.php - Backend unban user page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["user_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app->users->existsUser($app->request->query["user_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "User does not exist"));

	$user = $app->users->getUser($app->request->query["user_id"]);

	if ($app->request->query["user_id"] == $app->session->getCurrentSession()->user) {
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => "The current user cannot be banned"));
	} else {
		if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::BAN_USERS))
			$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => "You do not have permission to ban users"));

			if ($app->users->compareUserRank($app->session->getCurrentSession()->user, $app->request->query["reset_id"]) !== 1)
				$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => "Target user's group rank must be less than your own"));
	}

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		foreach ($app->bans->getBans($user->id) as $ban) {
			$ban->expires = time();
			$ban->modified = time();
			$app->bans->setBan($ban->id, $ban);
		}

		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "success", "content" => "User unbanned"));
	}

	$app->vars["user_id"] = $user->id;

	$app->page->setTitle("Unban User");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Unban User '<?php echo $app->vars["user_id"]; ?>'</h1>
					<p>Are you sure you want to unban this user?</p>
					<form>
						<input type="hidden" name="user_id" value="<?php echo $app->request->query["user_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2 mt-3" href="<?php echo Config::WEBROOT; ?>/backend/users/list_bans?user_id=<?php echo $app->vars["user_id"]; ?>">Back</a>
						<button class="btn btn-warning mt-3" type="submit">Unban User</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.delete", $app->request->query["user_id"]);

	$app->renderPage();
?>
