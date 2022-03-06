<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/articles/editor.php - Backend article editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	function checkQuery() {
		global $app;

		return	isset($app->request->query["article_title"]) &&
				isset($app->request->query["article_content"]) &&
				isset($app->request->query["article_summary"]) &&
				isset($app->request->query["article_id"]) &&
				isset($app->request->query["article_state"]) &&
				isset($app->request->query["article_category"]) &&
				isset($app->request->query["article_tags"]) &&
				isset($app->request->query["article_meta_desc"]) &&
				isset($app->request->query["article_meta_keys"]) &&
				isset($app->request->query["article_meta_robots"]) &&
				isset($app->request->query["article_options"]) &&
				is_array($app->request->query["article_options"]);
	}

	$app->vars["article_title"]		= "";
	$app->vars["article_content"]	= "";
	$app->vars["article_summary"]	= "";
	$app->vars["article_id"]		= "";
	$app->vars["article_published"]	= true;
	$app->vars["article_category"]	= null;
	$app->vars["article_tags"]		= "";
	$app->vars["article_meta_desc"]	= "";
	$app->vars["article_meta_keys"]	= "";
	$app->vars["article_meta_robots"] = "";
	$app->vars["article_options"]	= array();

	if (isset($app->request->query["edit_id"])) {
		if (!$app->content->existsArticle($app->request->query["edit_id"]))
			$app->redirect(Config::WEBROOT . "/backend/articles/list?me=Article does not exist");

		$article = $app->content->getArticle($app->request->query["edit_id"]);

		if ($article->author == $app->session->getCurrentSession()->user) {
			if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_ARTICLES_OWN))
				$app->redirect(Config::WEBROOT . "/backend/articles/list?me=You do not have permission to modify articles");
		} else {
			if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_ARTICLES))
				$app->redirect(Config::WEBROOT . "/backend/articles/list?me=You do not have permission to modify others' articles");
		}

		if (checkQuery()) {
			$id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["article_id"]) {
				if ($app->content->existsArticle($app->request->query["article_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => "The ID '{$app->request->query["article_id"]}' is taken! Using '$id'.");
				} else {
					if ($app->request->query["article_id"] == "")
						$id = $app->nameToId($app->request->query["article_title"]);
					else
						$id = $app->nameToId($app->request->query["article_id"]);

					$app->content->deleteArticle($app->request->query["edit_id"]);
				}
			}

			$article->title		= $app->request->query["article_title"];
			$article->content	= $app->request->query["article_content"];
			$article->summary	= $app->request->query["article_summary"];
			$article->id		= $id;
			$article->state		= $app->request->query["article_state"];
			$article->modified	= time();
			$article->category	= $app->content->getCategory($app->request->query["article_category"])->id;
			$article->tags		= $app->request->query["article_tags"];
			$article->meta_desc	= ($app->request->query["article_meta_desc"] != "") ? $app->request->query["article_meta_desc"] : $app->getSetting("meta_desc", "");
			$article->meta_keys	= ($app->request->query["article_meta_keys"] != "") ? $app->request->query["article_meta_keys"] : $app->getSetting("meta_keys", "");
			$article->meta_keys	= ($app->request->query["article_meta_robots"] != "") ? $app->request->query["article_meta_robots"] : $app->getSetting("meta_robots", "");
			$article->options	= $app->request->query["article_options"];

			$app->content->setArticle($id, $article);

			if ($app->request->query["article_id"] == "")
				$app->redirect(Config::WEBROOT . "/backend/articles/editor?edit_id=$id&ms=Changes saved.");

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit Article";

		$app->vars["article_title"]		= htmlentities($article->title);
		$app->vars["article_content"]	= htmlentities($article->content);
		$app->vars["article_summary"]	= htmlentities($article->summary);
		$app->vars["article_id"]		= $article->id;
		$app->vars["article_published"]	= $article->state == "published";
		$app->vars["article_category"]	= htmlentities($article->category);
		$app->vars["article_tags"]		= htmlentities($article->tags);
		$app->vars["article_meta_desc"]	= htmlentities($article->meta_desc);
		$app->vars["article_meta_keys"]	= htmlentities($article->meta_keys);
		$app->vars["article_meta_robots"]	= htmlentities($article->meta_robots);
		$app->vars["article_options"]	= $article->options;
	} else {
		$app->vars["title"] = "New Article";

		if (checkQuery()) {
			if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_ARTICLES_OWN))
				$app->redirect(Config::WEBROOT . "/backend/articles/list?me=You do not have permission to create articles");

			if ($app->request->query["article_id"] == "")
				$id = $app->nameToId($app->request->query["article_title"]);
			else
				$id = $app->nameToId($app->request->query["article_id"]);

			while ($app->content->existsArticle($id)) $id .= "_1";

			$article = new Article(array(
				"id"		=> $id,
				"title"		=> $app->request->query["article_title"],
				"content"	=> $app->request->query["article_content"],
				"summary"	=> $app->request->query["article_summary"],
				"state"		=> $app->request->query["article_state"],
				"author" 	=> $app->session->getCurrentSession()->user, // TODO: Change to active user
				"created"	=> time(),
				"modified"	=> time(),
				"category"	=> $app->request->query["article_category"],
				"tags"		=> $app->request->query["article_tags"],
				"meta_desc" => ($app->request->query["article_meta_desc"] != "") ? $app->request->query["article_meta_desc"] : $app->getSetting("meta_desc", ""),
				"meta_keys" => ($app->request->query["article_meta_keys"] != "") ? $app->request->query["article_meta_keys"] : $app->getSetting("meta_keys", ""),
				"meta_robots" => ($app->request->query["article_meta_robots"] != "") ? $app->request->query["article_meta_robots"] : $app->getSetting("meta_robots", ""),
				"hits"		=> 0,
				"options"	=> $app->request->query["article_options"]
			));

			$app->content->setArticle($id, $article);

			$app->redirect(Config::WEBROOT . "/backend/articles/editor?edit_id=$id&ms=Changes saved.");
		}
	}

	$app->page->setTitle($app->vars["title"]);

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php echo $app->vars["title"]; ?></h1>
				</div>
			</div>
			<div class="row">
				<form method="post" class="d-flex">
					<?php if (isset($app->request->query["edit_id"])) { ?>
						<input type="hidden" name="edit_id" value="<?php echo $app->request->query["edit_id"]; ?>" />
					<?php } ?>
					<div class="col col-lg-8 me-lg-2">
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item" role="presentation">
								<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#article_content">Content</button>
							</li>
							<li class="nav-item" role="presentation">
								<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#article_options">Options</button>
							</li>
						</ul>
						<div class="tab-content">
							<div id="article_content" class="tab-pane show active" role="tabpanel">
								<label for="article_title">Article Title:</label>
								<input type="text" class="form-control" name="article_title" value="<?php echo $app->vars["article_title"]; ?>" required />

								<label for="article_content">Article Content:</label>
								<textarea class="form-control" name="article_content" style="height: 300px; font-family: monospace;" required onkeydown="if(event.keyCode===9){var v=this.value,s=this.selectionStart,e=this.selectionEnd;this.value=v.substring(0, s)+'\t'+v.substring(e);this.selectionStart=this.selectionEnd=s+1;return false;}"><?php echo $app->vars["article_content"]; ?></textarea>

								<label for="article_summary">Article Summary:</label>
								<textarea class="form-control" name="article_summary" style="height: 170px;" required><?php echo $app->vars["article_summary"]; ?></textarea>
							</div>
							<div id="article_options" class="tab-pane" role="tabpanel">
								<label for="article_options[show_comments]">Show Comments:</label>
								<select class="form-control" name="article_options[show_comments]">
									<?php if ($app->getSetting("articles.show_comments", "yes") == "yes") { ?>

										<option value="yes" <?php if (($app->vars["article_options"]["show_comments"] ?? "yes") == "yes") echo "selected"; ?>>Yes (Default)</option>
										<option value="no" <?php if (($app->vars["article_options"]["show_comments"] ?? "yes") == "no") echo "selected"; ?>>No</option>
									<?php } else { ?>
										<option value="yes" <?php if (($app->vars["article_options"]["show_comments"] ?? "yes") == "yes") echo "selected"; ?>>Yes</option>
										<option value="no" <?php if (($app->vars["article_options"]["show_comments"] ?? "yes") == "no") echo "selected"; ?>>No (Default)</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col col-lg-4 ms-lg-2">
						<label for="article_id">Article ID:</label>
						<input type="text" class="form-control" name="article_id" placeholder="auto-generate" value="<?php echo $app->vars["article_id"]; ?>" />

						<label for="article_state">Article Status:</label>
						<select class="form-select" name="article_state">
							<option value="published" <?php if ($app->vars["article_published"]) echo "selected"; ?>>Published</option>
							<option value="unpublished" <?php if (!$app->vars["article_published"]) echo "selected"; ?>>Unpublished</option>
						</select>

						<label for="article_category">Category:</label>
						<?php RenderHelper::renderCategoryPicker("article_category", $app->vars["article_category"]); ?>

						<label for="article_tags">Article Tags:</label>
						<input type="text" class="form-control" name="article_tags" value="<?php echo $app->vars["article_tags"]; ?>" />

						<label for="article_meta_desc">Meta Description:</label>
						<textarea class="form-control" name="article_meta_desc" style="height: 160px;" placeholder="<?php echo $app->getSetting("meta_desc"); ?>"><?php echo $app->vars["article_meta_desc"]; ?></textarea>

						<label for="article_meta_keys">Meta Keywords:</label>
						<input type="text" class="form-control" name="article_meta_keys" placeholder="<?php echo $app->getSetting("meta_keys"); ?>" value="<?php echo $app->vars["article_meta_keys"]; ?>" />

						<label for="article_meta_robots">Meta Robots:</label>
						<input type="text" class="form-control" name="article_meta_robots" placeholder="<?php echo $app->getSetting("meta_robots"); ?>" value="<?php echo $app->vars["article_meta_robots"]; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 me-2" href="<?php echo Config::WEBROOT; ?>/backend/articles/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</form>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.articles.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
