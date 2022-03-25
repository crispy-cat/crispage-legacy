<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/ban_user.php - Backend user ban page

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
	}

	if (isset($app->request->query["ban_expires"])) {
		$expires = strtotime($app->request->query["ban_expires"]);
		if (!$expires)
			$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => "Invalid expiry time"));

		$reason = $app->request->query["ban_reason"] ?? null;

		$id = Randomizer::randomString(16, 62);
		$ban = new Ban(array(
			"id"		=> $id,
			"user"		=> $user->id,
			"created"	=> time(),
			"modified"	=> time(),
			"expires"	=> $expires,
			"reason"	=> $reason
		));

		$app->bans->setBan($id, $ban);
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "success", "content" => "User banned."));
	}

	$app->vars["user_id"] = $user->id;

	$app->page->setTitle("Delete Ban");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Ban User '<?php echo $app->vars["user_id"]; ?>'</h1>
					<form>
						<input type="hidden" name="user_id" value="<?php echo $app->request->query["user_id"]; ?>" />
						<label for="ban_reason">Ban reason:</label>
						<textarea class="form-control" name="ban_reason"></textarea>
						<label for="ban_expires">Ban expires:</label>
						<input type="datetime-local" class="form-control" name="ban_expires" required />
						<a class="btn btn-primary me-2 mt-3" href="<?php echo Config::WEBROOT; ?>/backend/users/list_bans?user_id=<?php echo $app->vars["user_id"]; ?>">Back</a>
						<button class="btn btn-danger mt-3" type="submit">Ban User</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.delete", $app->request->query["user_id"]);

	$app->renderPage();
?>
