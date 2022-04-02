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

	$session = $app->session->getCurrentSession();
	if ($app->vars["category"]->state != "published" && (!$session || !$app->users->userHasPermissions($session->user, UserPermissions::VIEW_UNPUBLISHED)))
		$app->error(new ApplicationException(404, "Page not found", "The page you requested could not be found. Please check the URL or try searching for it."));

	$app->vars["articles"] = $app->content->getArticles($app->request->route["item_id"]);
	$app->vars["subcategories"] = $app->content->getCategories($app->request->route["item_id"]);

	if (!$session || !$app->users->userHasPermissions($session->user, UserPermissions::VIEW_UNPUBLISHED)) {
		foreach ($app->vars["articles"] as $key => $article)
			if ($article->state != "published")
				array_splice($app->vars["articles"], $key, 1);
	}

	$app->vars["show"] = $app->request->query["show"] ?? 5;
	$app->vars["page"] = $app->request->query["page"] ?? 1;

	$app->vars["npages"] = Paginator::numPages($app->vars["articles"], (is_numeric($app->vars["show"])) ? $app->vars["show"] : 0);
	$app->vars["articles"] = Paginator::sPaginate($app->vars["articles"], $app->vars["show"], $app->vars["page"]);

	$app->page->options["show_title"] = $app->vars["category"]->options["show_title"] ?? $app->getSetting("categories.show_title", "yes");
	$app->page->options["show_sidebar"] = $app->vars["category"]->options["show_sidebar"] ?? $app->getSetting("categories.show_sidebar", "yes");

	$app->page->setTitle(htmlentities($app->vars["category"]->title));
	$app->page->metas["description"] = array("name" => "description", "content" => htmlentities($app->vars["category"]->meta_desc));
	$app->page->metas["keywords"] = array("name" => "keywords", "content" => htmlentities($app->vars["category"]->meta_keys));
	$app->page->metas["robots"] = array("name" => "robots", "content" => htmlentities($app->vars["category"]->meta_robots));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<?php echo $app->vars["category"]->content; ?>
			<hr />

			<form class="d-flex w-25 mb-3">
				<label for="show">Show: </label>
				<select class="form-select ms-2" name="show">
					<option value="5">5</option>
					<option value="15">15</option>
					<option value="30">30</option>
					<option value="60">60</option>
					<option value="120">120</option>
					<option value="240">240</option>
					<option value="480">480</option>
					<option value="all">All</option>
				</select>
				<button class="btn btn-primary ms-2" type="submit">Go</button>
			</form>
			<?php
				$baseurl = Config::WEBROOT . "/" . Router::getCategoryRoute($app->request->route["item_id"]) . "?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
				RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
			?>

			<?php if (count($app->vars["subcategories"])) { ?>
				<div class="card mt-3">
					<div class="card-body">
						<small>Subcategories</small>
						<?php foreach ($app->vars["subcategories"] as $sub) { ?>
							<h5><a href="<?php echo Config::WEBROOT . "/" . Router::getCategoryRoute($sub->id); ?>"><?php echo htmlentities($sub->title); ?></a></h5>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

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

				if (!count($app->vars["articles"]) && !count($app->vars["subcategories"]))
					echo "<p>No articles exist in this category.</p>";
			?>
		</div>
<?php
	});

	$app->events->trigger("content.category_view", $app->vars["category"]);

	$app->renderPage();
?>
