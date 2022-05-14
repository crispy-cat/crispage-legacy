<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/articles/list.php - Backend article list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle($app("i18n")->getString("articles"));

	$app->vars["cat"] = $app->request->query["cat"] ?? null;
	Paginator::paginationQuery($app->vars);

	$articles = $app("articles")->getAllArr(($app->vars["cat"]) ? array("category" => $app->vars["cat"]) : null, "title");
	
	Paginator::paginateNum($app->vars, $articles, "articles");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1><?php $app("i18n")("articles"); ?></h1>
					<span><?php $app("i18n")("show_c"); ?></span>
					<form class="d-flex">
						<?php RenderHelper::renderCategoryPicker("cat", null, array("title" => $app("i18n")->getString("all_categories"), "value" => "")); ?>
						<select class="form-select ms-2" name="show">
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
				</div>
				<div class="col-12 col-md-8 col-xxl-10">
					<div style="float: right;">
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/articles/editor" style="width: 110px;"><?php $app("i18n")("new_article"); ?></a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/articles/list?cat=" . (($app->vars["cat"]) ? $app->vars["cat"] : "") . "&show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["articles"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th><?php $app("i18n")("id"); ?></th>
									<th><?php $app("i18n")("title"); ?></th>
									<th><?php $app("i18n")("category"); ?></th>
									<th><?php $app("i18n")("author"); ?></th>
									<th><?php $app("i18n")("created"); ?></th>
									<th><?php $app("i18n")("modified"); ?></th>
									<th><?php $app("i18n")("state"); ?></th>
									<th><?php $app("i18n")("hits"); ?></th>
									<th><?php $app("i18n")("actions"); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["articles"] as $article) { ?>
									<tr>
										<td><code><?php echo $article->id; ?></code></td>
										<td><?php echo htmlentities($article->title); ?></td>
										<td><?php echo @htmlentities($app("categories")->get($article->category)->title); ?></td>
										<td><?php echo $article->author; ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $article->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $article->modified); ?></td>
										<td><?php echo htmlentities($article->state); ?></td>
										<td><?php echo $article->hits; ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/articles/editor?edit_id=<?php echo $article->id; ?>">
												<i class="bi bi-pencil"></i> <?php $app("i18n")("edit"); ?>
											</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/articles/delete?delete_id=<?php echo $article->id; ?>">
												<i class="bi bi-trash"></i> <?php $app("i18n")("delete"); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p><?php $app("i18n")("no_articles_match"); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.articles.list");

	$app->renderPage();
?>
