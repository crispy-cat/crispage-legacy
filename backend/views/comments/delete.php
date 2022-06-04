<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/comments/delete.php - Backend comment delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	if (!\Crispage\Assets\User::userHasPermissions(\Crispage\Assets\Session::getCurrentSession()->user, \Crispage\Users\UserPermissions::MODIFY_COMMENTS))
		$app->redirectWithMessages("/backend/comments/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_comments")));

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/comments/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("comments")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/comments/list", array("type" => "error", "content" => $app("i18n")->getString("comment_does_not_exist")));

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app("comments")->delete($app->request->query["delete_id"]);
		$app->redirectWithMessages("/backend/comments/list", array("type" => "success", "content" => $app("i18n")->getString("comment_deleted")));
	}

	$app->page->setTitle($app("i18n")->getString("delete_comment"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("delete_comment"); ?></h1>
					<p><?php $app("i18n")("sure_delete_comment"); ?></p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/comments/list"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-danger" type="submit"><?php $app("i18n")("delete"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.comments.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
