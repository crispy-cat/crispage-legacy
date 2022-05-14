<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/usergroups/delete.php - Backend user group delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_USERGROUPS))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_usergroups")));

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("usergroups")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("usergroup_does_not_exist")));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		if (User::compareUserRank(Session::getCurrentSession()->user, UserGroup::getGroupRank($app->request->query["delete_id"])) !== 1)
			$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("rank_less_than_own")));

		if (count($app("usergroups")->getAllArr()) < 2)
			$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("must_be_one_usergroup")));

		$app("usergroups")->delete($app->request->query["delete_id"]);
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "success", "content" => $app("i18n")->getString("usergroup_deleted")));
	}

	$app->vars["usergroup_name"] = htmlentities($app("usergroups")->get($app->request->query["delete_id"])->name);

	$app->page->setTitle($app("i18n")->getString("delete_v", null, $app->vars["usergroup_name"]));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("delete_v", null, $app->vars["usergroup_name"]); ?></h1>
					<p><?php $app("i18n")("sure_delete_usergroup"); ?></p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/list"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-danger" type="submit"><?php $app("i18n")("delete"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.usergroups.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
