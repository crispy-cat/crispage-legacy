<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/articles/delete.php - Backend article delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => "No ID Specified"));

	if (!$app("articles")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => "Article does not exist"));

	$article = $app("articles")->get($app->request->query["delete_id"]);

	if ($article->author == Session::getCurrentSession()->user) {
		if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_ARTICLES_OWN))
			$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => "You do not have permission to delete articles"));
	} else {
		if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_ARTICLES))
			$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => "You do not have permission to delete others' articles"));
	}

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app("articles")->delete($article->id);
		$app->redirectWithMessages("/backend/articles/list", array("type" => "success", "content" => "Article deleted."));
	}

	$app->vars["article_title"] = htmlentities($article->title);

	$app->page->setTitle("Delete {$app->vars["article_title"]}");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Delete '<?php echo $app->vars["article_title"]; ?>'</h1>
					<p>Are you sure you want to delete this article? This action cannot be undone!</p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/articles/list">Back</a>
						<button class="btn btn-danger" type="submit">Delete</button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.articles.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
