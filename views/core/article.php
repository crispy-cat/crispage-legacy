<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/article.php - Frontend article page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$app->vars["article"] = $app->content->getArticle($app->request->route["item_id"] ?? "");

	$session = $app->session->getCurrentSession();
	if ($app->vars["article"]->state != "published" && (!$session || !$app->users->userHasPermissions($session->user, UserPermissions::VIEW_UNPUBLISHED)))
		$app->error(new ApplicationException(404, "Page not found", "The page you requested could not be found. Please check the URL or try searching for it."));

	$app->page->options["show_title"] = $app->vars["article"]->options["show_title"] ?? $app->getSetting("articles.show_title", "yes");
	$app->page->options["show_sidebar"] = $app->vars["article"]->options["show_sidebar"] ?? $app->getSetting("articles.show_sidebar", "yes");

	if (isset($app->request->route["item_id"])) {
		$app->vars["article"]->hits++;
		$app->content->setArticle($app->request->route["item_id"], $app->vars["article"]);
	}

	$app->page->setTitle(htmlentities($app->vars["article"]->title));
	$app->page->metas["description"] = array("name" => "description", "content" => htmlentities($app->vars["article"]->meta_desc));
	$app->page->metas["keywords"] = array("name" => "keywords", "content" => htmlentities($app->vars["article"]->meta_keys));
	$app->page->metas["robots"] = array("name" => "robots", "content" => htmlentities($app->vars["article"]->meta_robots));
	$app->page->metas["author"] = array("name" => "author", "content" => $app->vars["article"]->author);

	$app->vars["date"] = date($app->getSetting("date_format_long", "Y, F j"), $app->vars["article"]->modified);
	$app->vars["word"] = ($app->vars["article"]->modified > $app->vars["article"]->created) ? "Updated" : "Published";
	if ($app->vars["article"]->tags != "") {
		$app->vars["stags"] = "Tags: ";
		foreach (explode(",", $app->vars["article"]->tags) as $tag)
			$app->vars["stags"] .= "<span class=\"badge bg-primary me-1\">". htmlentities($tag) . "</span>";
	}

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<?php
				echo $app->vars["article"]->content;

				if (($app->vars["article"]->options["show_info"] ?? $app->getSetting("articles.show_info", "yes")) == "yes") {
			?>
				<ul id="article_info" class="list-group page-meta">
					<li class="list-group-item">Author: <?php echo $app->vars["article"]->author; ?></li>
					<li class="list-group-item"><?php echo $app->vars["word"]; ?>: <?php echo $app->vars["date"]; ?></li>
					<li class="list-group-item">Category: <?php echo htmlentities($app->content->getCategory($app->vars["article"]->category)->title); ?></li>
					<?php if ($app->vars["article"]->tags != "") { ?>
						<li class="list-group-item"><?php echo $app->vars["stags"]; ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
<?php
	});

	$app->events->trigger("content.article_view", $app->vars["article"]);

	$app->renderPage();
?>
