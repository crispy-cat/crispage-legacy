<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/articles/list.php - Backend article list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Articles");

	$app->vars["cat"] = $app->request->query["cat"] ?? null;
	$app->vars["show"] = $app->request->query["show"] ?? 15;
	$app->vars["page"] = $app->request->query["page"] ?? 1;

	$articles = $app->content->getArticles($app->vars["cat"]);

	$app->vars["npages"] = Paginator::numPages($articles, (is_numeric($app->vars["show"])) ? $app->vars["show"] : 0);
	$app->vars["articles"] = Paginator::sPaginate($articles, $app->vars["show"], $app->vars["page"]);
	
	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1>Articles</h1>
					<span>Show only:</span>
					<form class="d-flex">
						<?php RenderHelper::renderCategoryPicker("cat", null, array("title" => "All Categories", "value" => "")); ?>
						<select class="form-select ms-2" name="show">
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
				</div>
				<div class="col-12 col-md-8 col-xxl-10">
					<div style="float: right;">
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/articles/editor" style="width: 110px;">New Article</a>
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
									<th>Id</th>
									<th>Title</th>
									<th>Category</th>
									<th>Author</th>
									<th>Created</th>
									<th>Modified</th>
									<th>State</th>
									<th>Hits</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["articles"] as $article) { ?>
									<tr>
										<td><code><?php echo $article->id; ?></code></td>
										<td><?php echo htmlentities($article->title); ?></td>
										<td><?php echo @htmlentities($app->content->getCategory($article->category)->title); ?></td>
										<td><?php echo $article->author; ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $article->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $article->modified); ?></td>
										<td><?php echo htmlentities($article->state); ?></td>
										<td><?php echo $article->hits; ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/articles/editor?edit_id=<?php echo $article->id; ?>"><i class="bi bi-pencil"></i> Edit</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/articles/delete?delete_id=<?php echo $article->id; ?>"><i class="bi bi-trash"></i> Delete</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No articles match your criteria!</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.articles.list");

	$app->renderPage();
?>
