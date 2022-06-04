<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/articles/delete.php - Backend article delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	if (!$app("articles")->exists($app->request->query["delete_id"]))
		$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("article_does_not_exist")));

	$article = $app("articles")->get($app->request->query["delete_id"]);

	if ($article->author == \Crispage\Assets\Session::getCurrentSession()->user) {
		if (!\Crispage\Assets\User::userHasPermissions(\Crispage\Assets\Session::getCurrentSession()->user, \Crispage\Users\UserPermissions::MODIFY_ARTICLES_OWN))
			$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_articles")));
	} else {
		if (!\Crispage\Assets\User::userHasPermissions(\Crispage\Assets\Session::getCurrentSession()->user, \Crispage\Users\UserPermissions::MODIFY_ARTICLES))
			$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_articles_others")));
	}

	if (isset($app->request->query["confirm"]) && $app->request->query["confirm"]) {
		$app("articles")->delete($article->id);
		$app->redirectWithMessages("/backend/articles/list", array("type" => "success", "content" => $app("i18n")->getString("article_deleted")));
	}

	$app->vars["article_title"] = htmlentities($article->title);

	$app->page->setTitle($app("i18n")->getString("delete_v", null, $app->vars["article_title"]));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("delete_v", null, $app->vars["article_title"]); ?></h1>
					<p><?php $app("i18n")("sure_delete_article"); ?></p>
					<form class="d-flex">
						<input type="hidden" name="delete_id" value="<?php echo $app->request->query["delete_id"]; ?>" />
						<input type="hidden" name="confirm" value="1" />
						<a class="btn btn-primary me-2" href="<?php echo Config::WEBROOT; ?>/backend/articles/list"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-danger" type="submit"><?php $app("i18n")("delete"); ?></button>
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.articles.delete", $app->request->query["delete_id"]);

	$app->renderPage();
?>
