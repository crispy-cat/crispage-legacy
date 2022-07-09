<?php
	/*
		Crispage - A lightweight CMS for developers
		views/core/category.php - Frontend category page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/header.php";

	$app->vars["category"] = $app("categories")->get($app->request->route["item_id"]);

	$session = \Crispage\Assets\Session::getCurrentSession();
	if (!$app->vars["category"] || ($app->vars["category"]->state != "published" && (!$session || !\Crispage\Assets\User::userHasPermissions($session->user, \Crispage\Users\UserPermissions::VIEW_UNPUBLISHED))))
		$app->error(new \Crispage\ApplicationException(404, $app("i18n")->getString("page_not_found"), $app("i18n")->getString("page_not_found_ex")));

	$app->vars["articles"] = $app("articles")->getAllArr(array("category" => $app->request->route["item_id"]), "modified");
	$app->vars["subcategories"] = $app("categories")->getAllArr(array("parent" => $app->request->route["item_id"]), "title");

	if (!$session || !\Crispage\Assets\User::userHasPermissions($session->user, \Crispage\Users\UserPermissions::VIEW_UNPUBLISHED)) {
		foreach ($app->vars["articles"] as $key => $article)
			if ($article->state != "published")
				array_splice($app->vars["articles"], $key, 1);
	}

	$app->vars["show"] = (is_numeric($app->request->query["show"])) ? $app->request->query["show"] : 5;
	$app->vars["page"] = (is_numeric($app->request->query["page"])) ? $app->request->query["page"] : 1;

	$app->vars["npages"] = \Crispage\Helpers\Paginator::numPages($app->vars["articles"], $app->vars["show"]);
	$app->vars["articles"] = \Crispage\Helpers\Paginator::Paginate($app->vars["articles"], $app->vars["show"], $app->vars["page"]);

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
				<label for="show"><?php $app("i18n")("show_c", null, ""); ?></label>
				<select class="form-select ms-2" name="show">
					<option value="5">5</option>
					<option value="15">15</option>
					<option value="30">30</option>
					<option value="60">60</option>
					<option value="120">120</option>
					<option value="240">240</option>
					<option value="480">480</option>
					<option value="all"><?php $app("i18n")("all"); ?></option>
				</select>
				<button class="btn btn-primary ms-2" type="submit"><?php $app("i18n")("go"); ?></button>
			</form>
			<?php
				$baseurl = \Config::WEBROOT . "/" . \Crispage\Routing\Router::getCategoryRoute($app->request->route["item_id"]) . "?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
				\Crispage\Helpers\RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
			?>

			<?php if (count($app->vars["subcategories"])) { ?>
				<div class="card mt-3">
					<div class="card-body">
						<small><?php $app("i18n")("subcategories"); ?></small>
						<?php foreach ($app->vars["subcategories"] as $sub) { ?>
							<h5><a href="<?php echo \Config::WEBROOT . "/" . \Crispage\Routing\Router::getCategoryRoute($sub->id); ?>"><?php echo htmlentities($sub->title); ?></a></h5>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

			<?php
				foreach ($app->vars["articles"] as $article) {
					if ($article->tags != "") {
						$stags = $app("i18n")->getString("tags_v", null, "");
						foreach (explode(",", $article->tags) as $tag)
							$stags .= "<span class=\"badge bg-primary me-1\">" . htmlentities($tag) . "</span>";
					}
			?>
					<div class="card mt-3">
						<div class="card-body">
							<h2 class="card-title"><a href="<?php echo \Config::WEBROOT . "/" . \Crispage\Routing\Router::getArticleRoute($article->id); ?>"><?php echo htmlentities($article->title); ?></a></h2>
							<?php echo $article->summary; ?>
						</div>
						<ul class="list-group list-group-flush">
							<li class="list-group-item">
								<?php echo $app("i18n")->getString(($article->modified > $article->created) ? "updated" : "published") . ": " . date($app->getSetting("date_format_long", "Y, F j"), $article->modified); ?>
							</li>
							<?php if ($article->tags != "") { ?>
								<li class="list-group-item"><?php echo $stags; ?></li>
							<?php } ?>
						</ul>
					</div>
			<?php
				}

				if (!count($app->vars["articles"]) && !count($app->vars["subcategories"]))
					echo "<p>" . $app("i18n")->getString("no_articles_in_category") . "</p>";
			?>
		</div>
<?php
	});

	$app->events->trigger("frontend.view.category", $app->vars["category"]);

	$app->renderPage();
?>
