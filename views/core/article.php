<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/article.php - Frontend article page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/header.php";

	$app->vars["article"] = $app("articles")->get($app->request->route["item_id"] ?? "");

	$session = \Crispage\Assets\Session::getCurrentSession();
	if (!$app->vars["article"] || ($app->vars["article"]->state != "published" && (!$session || !User::userHasPermissions($session->user, \Crispage\Assets\UserPermissions::VIEW_UNPUBLISHED))))
		$app->error(new \Crispage\ApplicationException(404, $app("i18n")->getString("page_not_found"), $app("i18n")->getString("page_not_found_ex")));

	$app->page->options["show_title"] = $app->vars["article"]->options["show_title"] ?? $app->getSetting("articles.show_title", "yes");
	$app->page->options["show_sidebar"] = $app->vars["article"]->options["show_sidebar"] ?? $app->getSetting("articles.show_sidebar", "yes");

	if (isset($app->request->route["item_id"])) {
		$app->vars["article"]->hits++;
		$app("articles")->set($app->request->route["item_id"], $app->vars["article"]);
	}

	$app->page->setTitle(htmlentities($app->vars["article"]->title));
	$app->page->metas["description"] = array("name" => "description", "content" => htmlentities($app->vars["article"]->meta_desc));
	$app->page->metas["keywords"] = array("name" => "keywords", "content" => htmlentities($app->vars["article"]->meta_keys));
	$app->page->metas["robots"] = array("name" => "robots", "content" => htmlentities($app->vars["article"]->meta_robots));
	$app->page->metas["author"] = array("name" => "author", "content" => $app->vars["article"]->author);

	$app->vars["date"] = date($app->getSetting("date_format_long", "Y, F j"), $app->vars["article"]->modified);
	$app->vars["word"] = $app("i18n")->getString(($app->vars["article"]->modified > $app->vars["article"]->created) ? "updated" : "published");
	if ($app->vars["article"]->tags != "") {
		$app->vars["stags"] = $app("i18n")->getString("tags_v", null, "");
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
					<li class="list-group-item"><?php $app("i18n")("author_v", null, $app->vars["article"]->author); ?></li>
					<li class="list-group-item"><?php echo $app->vars["word"]; ?>: <?php echo $app->vars["date"]; ?></li>
					<li class="list-group-item"><?php $app("i18n")("category_v", null, htmlentities($app("categories")->get($app->vars["article"]->category)->title)); ?></li>
					<?php if ($app->vars["article"]->tags != "") { ?>
						<li class="list-group-item"><?php echo $app->vars["stags"]; ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
<?php
	});

	$app->events->trigger("frontend.view.article", $app->vars["article"]);

	$app->renderPage();
?>
