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
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("bans")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("ban_does_not_exist")));

	$ban = $app("bans")->get($app->request->query["delete_id"]);

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::BAN_USERS))
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$ban->user}", array("type" => "error", "content" => $app("i18n")->getString("no_permission_bans"));

	if (User::compareUserRank(Session::getCurrentSession()->user, $ban->user) !== 1)
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => $app("i18n")->getString("rank_less_than_own")));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app("bans")->delete($ban->id);
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$ban->user}", array("type" => "success", "content" => $app("i18n")->getString("ban_deleted")));
	}

	$app->vars["ban_user"] = $ban->user;

	$app->page->setTitle($app("i18n")->getString("delete_ban"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("delete_ban"); ?></h1>
					<p><?php $app("i18n")("sure_delete_ban"); ?></p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/users/list_bans?user_id=<?php echo $app->vars["ban_user"]; ?>"><?php $app("i18n")("back"); ?></a>
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
