<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/articles/editor.php - Backend article editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$currentUser = Session::getCurrentSession()->user;
	$formFilled = FormHelper::formFieldsFilled(
		"article_title", "article_content", "article_summary", "article_id",
		"article_state", "article_category", "article_tags", "article_meta_desc",
		"article_meta_keys", "article_meta_robots", "article_options"
	) && is_array($app->request->query["article_options"]);

	$app->vars["article"] = new Article(array());

	if (isset($app->request->query["edit_id"])) {
		$app->vars["title"] = $app("i18n")->getString("edit_article");

		if (!$app("articles")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("article_does_not_exist")));

		$app->vars["article"] = $app("articles")->get($app->request->query["edit_id"]);

		if ($app->vars["article"]->author == $currentUser) {
			if (!User::userHasPermissions($currentUser, UserPermissions::MODIFY_ARTICLES_OWN))
				$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_articles")));
		} else {
			if (!User::userHasPermissions($currentUser, UserPermissions::MODIFY_ARTICLES))
				$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_articles_others")));
		}

		if ($formFilled) {
			$app->vars["article"]->id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["article_id"]) {
				if ($app("articles")->exists($app->request->query["article_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => $app("i18n")->getString("id_taken_using", null, $app->request->query["article_id"], $app->vars["article"]->id));
				} else {
					if ($app->request->query["article_id"] == "")
						$app->vars["article"]->id = $app->nameToId($app->request->query["article_title"]);
					else
						$app->vars["article"]->id = $app->nameToId($app->request->query["article_id"]);

					$app("articles")->delete($app->request->query["edit_id"]);
				}
			}

			$app->vars["article"]->title	= $app->request->query["article_title"];
			$app->vars["article"]->content	= $app->request->query["article_content"];
			$app->vars["article"]->summary	= $app->request->query["article_summary"];
			$app->vars["article"]->state	= $app->request->query["article_state"];
			$app->vars["article"]->modified	= time();
			$app->vars["article"]->category	= $app->request->query["article_category"];
			$app->vars["article"]->tags		= $app->request->query["article_tags"];
			$app->vars["article"]->meta_desc= ($app->request->query["article_meta_desc"] != "") ? $app->request->query["article_meta_desc"] : $app->getSetting("meta_desc", "");
			$app->vars["article"]->meta_keys= ($app->request->query["article_meta_keys"] != "") ? $app->request->query["article_meta_keys"] : $app->getSetting("meta_keys", "");
			$app->vars["article"]->meta_robots= ($app->request->query["article_meta_robots"] != "") ? $app->request->query["article_meta_robots"] : $app->getSetting("meta_robots", "");
			$app->vars["article"]->options	= $app->request->query["article_options"];

			$app("articles")->set($app->vars["article"]->id, $app->vars["article"]);

			if ($app->request->query["article_id"] == "")
				$app->redirectWithMessages("/backend/articles/editor?edit_id=" . $app->vars["article"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}
	} else {
		$app->vars["title"] = $app("i18n")->getString("new_article");

		if ($formFilled) {
			if (!User::userHasPermissions($currentUser, UserPermissions::MODIFY_ARTICLES_OWN))
				$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_articles")));

			if ($app->request->query["article_id"] == "")
				$app->vars["article"]->id = $app->nameToId($app->request->query["article_title"]);
			else
				$app->vars["article"]->id = $app->nameToId($app->request->query["article_id"]);

			while ($app("articles")->exists($app->vars["article"]->id)) $app->vars["article"]->id .= "_1";

			$app->vars["article"]->title	= $app->request->query["article_title"];
			$app->vars["article"]->content	= $app->request->query["article_content"];
			$app->vars["article"]->summary	= $app->request->query["article_summary"];
			$app->vars["article"]->state	= $app->request->query["article_state"];
			$app->vars["article"]->author	= $currentUser;
			$app->vars["article"]->created	= time();
			$app->vars["article"]->modified	= time();
			$app->vars["article"]->category	= $app->request->query["article_category"];
			$app->vars["article"]->tags		= $app->request->query["article_tags"];
			$app->vars["article"]->meta_desc= ($app->request->query["article_meta_desc"] != "") ? $app->request->query["article_meta_desc"] : $app->getSetting("meta_desc", "");
			$app->vars["article"]->meta_keys= ($app->request->query["article_meta_keys"] != "") ? $app->request->query["article_meta_keys"] : $app->getSetting("meta_keys", "");
			$app->vars["article"]->meta_robots= ($app->request->query["article_meta_robots"] != "") ? $app->request->query["article_meta_robots"] : $app->getSetting("meta_robots", "");
			$app->vars["article"]->hits		= 0;
			$app->vars["article"]->options	= $app->request->query["article_options"];

			$app("articles")->set($app->vars["article"]->id, $app->vars["article"]);

			$app->redirectWithMessages("/backend/articles/editor?edit_id=" . $app->vars["article"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));
		}
	}

	$app->page->setTitle($app->vars["title"]);

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php echo $app->vars["title"]; ?></h1>
					<?php if ($app->vars["article"]->id != "") { ?>
						<a target="_blank" href="<?php echo Config::WEBROOT . "/" . Router::getArticleRoute($app->vars["article"]->id); ?>"><?php $app("i18n")("view_article"); ?></a>
					<?php } ?>
				</div>
			</div>
			<form method="post">
				<div class="row">
					<?php if (isset($app->request->query["edit_id"])) { ?>
						<input type="hidden" name="edit_id" value="<?php echo $app->request->query["edit_id"]; ?>" />
					<?php } ?>
					<div class="col-12 col-lg-8 pe-lg-2">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item" role="presentation">
								<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#article_content"><?php $app("i18n")("content"); ?></button>
							</li>
							<li class="nav-item" role="presentation">
								<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#article_options"><?php $app("i18n")("options"); ?></button>
							</li>
						</ul>
						<div class="tab-content">
							<div id="article_content" class="tab-pane show active" role="tabpanel">
								<label for="article_title"><?php $app("i18n")("article_title_c"); ?></label>
								<input type="text" class="form-control" name="article_title" value="<?php echo htmlentities($app->vars["article"]->title); ?>" required />

								<label for="article_content"><?php $app("i18n")("article_content_c"); ?></label>
								<?php RenderHelper::renderEditor("article_content", htmlentities($app->vars["article"]->content)); ?>

								<label for="article_summary"><?php $app("i18n")("article_summary"); ?></label>
								<textarea class="form-control" name="article_summary" style="height: 170px;" required><?php echo htmlentities($app->vars["article"]->summary); ?></textarea>
							</div>
							<div id="article_options" class="tab-pane" role="tabpanel">
								<label for="article_options[show_comments]"><?php $app("i18n")("show_comments_c"); ?></label>
								<?php RenderHelper::renderYesNo("article_options[show_comments]", $app->vars["article"]->options["show_comments"] ?? $app->getSetting("articles.show_comments", "yes")); ?>

								<label for="article_options[show_info]"><?php $app("i18n")("show_article_info_c"); ?></label>
								<?php RenderHelper::renderYesNo("article_options[show_comments]", $app->vars["article"]->options["show_info"] ?? $app->getSetting("articles.show_info", "yes")); ?>

								<label for="article_options[show_title]"><?php $app("i18n")("show_title_c"); ?></label>
								<?php RenderHelper::renderYesNo("article_options[show_comments]", $app->vars["article"]->options["show_title"] ?? $app->getSetting("articles.show_title", "yes")); ?>

								<label for="article_options[show_sidebar]"><?php $app("i18n")("show_sidebar_c"); ?></label>
								<?php RenderHelper::renderYesNo("article_options[show_comments]", $app->vars["article"]->options["show_sidebar"] ?? $app->getSetting("articles.show_sidebar", "yes")); ?>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="article_id"><?php $app("i18n")("article_id_c"); ?></label>
						<input type="text" class="form-control" name="article_id" placeholder="auto-generate" value="<?php echo htmlentities($app->vars["article"]->id); ?>" />

						<label for="article_state"><?php $app("i18n")("article_state_c"); ?></label>
						<select class="form-select" name="article_state">
							<option value="published" <?php if ($app->vars["article"]->state == "published") echo "selected"; ?>><?php $app("i18n")("published"); ?></option>
							<option value="unpublished" <?php if ($app->vars["article"]->state == "unpublished") echo "selected"; ?>><?php $app("i18n")("unpublished"); ?></option>
						</select>

						<label for="article_category"><?php $app("i18n")("article_category_c"); ?></label>
						<?php RenderHelper::renderCategoryPicker("article_category", htmlentities($app->vars["article"]->category)); ?>

						<label for="article_tags"><?php $app("i18n")("article_tags_c"); ?></label>
						<input type="text" class="form-control" name="article_tags" value="<?php echo htmlentities($app->vars["article"]->tags); ?>" />

						<label for="article_meta_desc"><?php $app("i18n")("meta_description_c"); ?></label>
						<textarea class="form-control" name="article_meta_desc" style="height: 160px;" placeholder="<?php echo $app->getSetting("meta_desc"); ?>"><?php echo htmlentities($app->vars["article"]->meta_desc); ?></textarea>

						<label for="article_meta_keys"><?php $app("i18n")("meta_keywords_c"); ?></label>
						<input type="text" class="form-control" name="article_meta_keys" placeholder="<?php echo $app->getSetting("meta_keys"); ?>" value="<?php echo htmlentities($app->vars["article"]->meta_keys); ?>" />

						<label for="article_meta_robots"><?php $app("i18n")("meta_robots_c"); ?></label>
						<input type="text" class="form-control" name="article_meta_robots" placeholder="<?php echo $app->getSetting("meta_robots"); ?>" value="<?php echo htmlentities($app->vars["article"]->meta_robots); ?>" />

						<a class="btn btn-secondary btn-lg mt-3 me-2" href="<?php echo Config::WEBROOT; ?>/backend/articles/list" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("save"); ?></button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.articles.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
