<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/comments/list.php - Backend comment list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle($app("i18n")->getString("comments"));

	$app->vars["art"] = $app->request->query["art"] ?? null;
	Paginator::paginationQuery($app->vars);

	$comments = $app("comments")->getAllArr(($app->vars["art"]) ? array("article" => $app->vars["art"]) : null, "modified");

	Paginator::paginateNum($app->vars, $comments, "comments");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1><?php $app("i18n")("comments"); ?></h1>
					<span><?php $app("i18n")("show_c"); ?></span>
					<form class="d-flex">
						<?php RenderHelper::renderArticlePicker("art", null, array("title" => $app("i18n")->getString("all_articles"), "value" => "")); ?>
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
									<th><?php $app("i18n")("id"); ?></th>
									<th><?php $app("i18n")("author"); ?></th>
									<th><?php $app("i18n")("article"); ?></th>
									<th><?php $app("i18n")("comment"); ?></th>
									<th><?php $app("i18n")("created"); ?></th>
									<th><?php $app("i18n")("modified"); ?></th>
									<th><?php $app("i18n")("actions"); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["comments"] as $comment) { ?>
									<tr>
										<td><code><?php echo $comment->id; ?></code></td>
										<td><?php echo $comment->author; ?></td>
										<td><?php echo @htmlentities($app("articles")->get($comment->article)->title); ?></td>
										<td><?php echo htmlentities($comment->content); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $comment->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $comment->modified); ?></td>
										<td>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/comments/delete?delete_id=<?php echo $comment->id; ?>">
												<i class="bi bi-trash"></i> <?php $app("i18n")("delete"); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p><?php $app("i18n")("no_comments_match"); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.comments.list");

	$app->renderPage();
?>
