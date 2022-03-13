<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/comments/list.php - Backend comment list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Comments");

	$app->vars["art"] = $app->request->query["art"] ?? "";
	$app->vars["show"] = $app->request->query["show"] ?? 15;
	$app->vars["page"] = $app->request->query["page"] ?? 1;

	$comments = ($app->vars["art"] != "") ? $app->comments->getComments($app->vars["art"]) : $app->comments->getComments();

	$app->vars["npages"] = Paginator::numPages($comments, (is_numeric($app->vars["show"])) ? $app->vars["show"] : 0);

	if (is_numeric($app->vars["show"]))
		$app->vars["comments"] = Paginator::paginate($comments, $app->vars["show"], (is_numeric($app->vars["page"])) ? $app->vars["page"] : 1);
	else
		$app->vars["comments"] = $comments;

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4">
					<h1>Comments</h1>
					<span>Show only:</span>
					<form class="d-flex">
						<?php RenderHelper::renderArticlePicker("art", null, array("title" => "All Articles", "value" => "")); ?>
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
				<div class="col-12 col-md-8">
					<div style="float: right;">
						<?php
							$baseurl = Config::WEBROOT . "/backend/comments/list?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["comments"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Id</th>
									<th>Author</th>
									<th>Article</th>
									<th>Comment</th>
									<th>Created</th>
									<th>Modified</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["comments"] as $comment) { ?>
									<tr>
										<td><code><?php echo $comment->id; ?></code></td>
										<td><?php echo $comment->author; ?></td>
										<td><?php echo ($app->content->getArticle($comment->article)) ? htmlentities($app->content->getArticle($comment->article)->title) : "[deleted article]"; ?></td>
										<td><?php echo htmlentities($comment->content); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $comment->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $comment->modified); ?></td>
										<td>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/comments/delete?delete_id=<?php echo $comment->id; ?>"><i class="bi bi-trash"></i> Delete</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No comments match your criteria!</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.comments.list");

	$app->renderPage();
?>
