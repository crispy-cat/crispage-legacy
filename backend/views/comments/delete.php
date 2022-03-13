<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/comments/delete.php - Backend comment delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_COMMENTS))
		$app->redirect(Config::WEBROOT . "/backend/comments/list?me=You do not have permission to delete comments");

	if (!isset($app->request->query["delete_id"]))
		$app->redirect(Config::WEBROOT . "/backend/comments/list?me=No ID Specified");

	if (!$app->comments->existsComment($app->request->query["delete_id"]))
		$app->redirect(Config::WEBROOT . "/backend/comments/list?me=Comment does not exist");

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app->comments->deleteComment($app->request->query["delete_id"]);
		$app->redirect(Config::WEBROOT . "/backend/comments/list?ms=Comment deleted.");
	}

	$app->page->setTitle("Delete Comment");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete Comment</h1>
					<p>Are you sure you want to delete this comment? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/comments/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.comments.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>