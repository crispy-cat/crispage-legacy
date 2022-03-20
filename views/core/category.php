<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/category.php - Frontend category page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/core/header.php";

	$app->vars["category"] = $app->content->getCategory($app->request->route["item_id"]);
	$app->vars["articles"] = $app->content->getArticles($app->request->route["item_id"]);

	$app->page->setTitle(htmlentities($app->vars["category"]->title));
	$app->page->metas["description"] = array("name" => "description", "content" => htmlentities($app->vars["category"]->meta_desc));
	$app->page->metas["keywords"] = array("name" => "keywords", "content" => htmlentities($app->vars["category"]->meta_keys));
	$app->page->metas["robots"] = array("name" => "robots", "content" => htmlentities($app->vars["category"]->meta_robots));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<?php echo $app->vars["category"]->content; ?>
			<hr />
			<?php
				foreach ($app->vars["articles"] as $article) {
					if ($article->tags != "") {
						$stags = "Tags: ";
						foreach (explode(",", $article->tags) as $tag)
							$stags .= "<span class=\"badge bg-primary me-1\">" . htmlentities($tag) . "</span>";
					}
			?>
					<div class="card mt-3">
						<div class="card-body">
							<h2 class="card-title"><a href="<?php echo Config::WEBROOT . "/" . Router::getArticleRoute($article->id); ?>"><?php echo htmlentities($article->title); ?></a></h2>
							<?php echo $article->summary; ?>
						</div>
						<ul class="list-group list-group-flush">
							<li class="list-group-item">
								<?php echo (($article->modified > $article->created) ? "Updated" : "Published") . ":" . date($app->getSetting("date_format_long", "Y, F j"), $article->modified); ?>
							</li>
							<?php if ($article->tags != "") { ?>
								<li class="list-group-item"><?php echo $stags; ?></li>
							<?php } ?>
						</ul>
					</div>
			<?php
				}

				if (!count($app->vars["articles"]))
					echo "<p>No articles exist in this category.</p>";
			?>
		</div>
<?php
	});

	$app->events->trigger("content.category_view", $app->vars["category"]);

	$app->renderPage();
?>
