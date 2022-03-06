<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/delete_ban.php - Backend user ban delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["delete_id"]))
		$app->redirect(Config::WEBROOT . "/backend/users/list?me=No ID Specified");

	if (!$app->bans->existsBan($app->request->query["delete_id"]))
		$app->redirect(Config::WEBROOT . "/backend/users/list?me=Ban does not exist");

	$ban = $app->bans->getBan($app->request->query["delete_id"]);

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::BAN_USERS))
		$app->redirect(Config::WEBROOT . "/backend/users/list_bans?user_id={$ban->user}&me=You do not have permission to delete bans");

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app->bans->deleteBan($ban->id);
		$app->redirect(Config::WEBROOT . "/backend/users/list_bans?user_id={$ban->user}&ms=Ban deleted.");
	}

	$app->vars["ban_user"] = $ban->user;

	$app->page->setTitle("Delete Ban");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete Ban</h1>
					<p>Are you sure you want to delete this ban? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/users/list_bans?user_id=<?php echo $app->vars["ban_user"]; ?>">Back</a>
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
