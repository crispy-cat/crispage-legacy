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
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("users")->exists($app->request->query["user_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("user_does_not_exist")));

	$user = $app("users")->get($app->request->query["user_id"]);

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::BAN_USERS))
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => $app("i18n")->getString("no_permission_bans")));

	if (User::compareUserRank(Session::getCurrentSession()->user, $app->request->query["reset_id"]) !== 1)
		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "error", "content" => $app("i18n")->getString("rank_less_than_own")));
	
	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		foreach ($app("bans")->getAll(array("user" => $user->id)) as $ban) {
			$ban->expires = time();
			$ban->modified = time();
			$app("bans")->set($ban->id, $ban);
		}

		$app->redirectWithMessages("/backend/users/list_bans?user_id={$user->id}", array("type" => "success", "content" => $app("i18n")->getString("user_unbanned")));
	}

	$app->vars["user_id"] = $user->id;

	$app->page->setTitle($app("i18n")->getString("unban_user"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("unban_user"); ?></h1>
					<form>
						<input type="hidden" name="user_id" value="<?php echo $app->request->query["user_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2 mt-3" href="<?php echo Config::WEBROOT; ?>/backend/users/list_bans?user_id=<?php echo $app->vars["user_id"]; ?>"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-warning mt-3" type="submit"><?php $app("i18n")("unban_user"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.delete", $app->request->query["user_id"]);

	$app->renderPage();
?>
