<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/categories/editor.php - Backend category editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$currentUser = Session::getCurrentSession()->user;
	$formFilled = FormHelper::formFieldsFilled(
		"category_title", "category_content", "category_id", "category_state",
		"category_parent", "category_tags", "category_meta_desc",
		"category_meta_keys", "category_meta_robots", "category_options"
	) && is_array($app->request->query["category_options"]);

	$app->vars["category"] = new Category(array());

	if (!User::userHasPermissions($currentUser, UserPermissions::MODIFY_CATEGORIES))
		$app->redirectWithMessages("/backend/categories/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_categories")));

	if (isset($app->request->query["edit_id"])) {
		if (!$app("categories")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/categories/list", array("type" => "error", "content" => $app("i18n")->getString("category_does_not_exist")));

		$app->vars["category"] = $app("categories")->get($app->request->query["edit_id"]);

		if ($formFilled) {
			$app->vars["category"]->id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["category_id"]) {
				if ($app("categories")->exists($app->request->query["category_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => $app("i18n")->getString("id_taken_using", null, $app->request->query["category_id"], $app->vars["category"]->id));
				} else {
					if ($app->request->query["category_id"] == "")
						$app->vars["category"]->id = $app->nameToId($app->request->query["category_title"]);
					else
						$app->vars["category"]->id = $app->nameToId($app->request->query["category_id"]);

					$app("categories")->delete($app->request->query["edit_id"]);
				}
			}

			$parent = $app->request->query["category_parent"];
			if ($parent == $app->vars["category"]->id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_cannot_be_self"));
			}

			$app->vars["category"]->title	= $app->request->query["category_title"];
			$app->vars["category"]->content	= $app->request->query["category_content"];
			$app->vars["category"]->id		= $app->vars["category"]->id;
			$app->vars["category"]->state	= $app->request->query["category_state"];
			$app->vars["category"]->modified	= time();
			$app->vars["category"]->parent	= $parent;
			$app->vars["category"]->tags		= $app->request->query["category_tags"];
			$app->vars["category"]->meta_desc= ($app->request->query["category_meta_desc"] != "") ? $app->request->query["category_meta_desc"] : $app->getSetting("meta_desc", "");
			$app->vars["category"]->meta_keys= ($app->request->query["category_meta_keys"] != "") ? $app->request->query["category_meta_keys"] : $app->getSetting("meta_keys", "");
			$app->vars["category"]->meta_robots= ($app->request->query["category_meta_robots"] != "") ? $app->request->query["category_meta_robots"] : $app->getSetting("meta_robots", "");
			$app->vars["category"]->options	= $app->request->query["category_options"];

			$app("categories")->set($app->vars["category"]->id, $app->vars["category"]);

			if (Asset::parentLoop("categories", $app->vars["category"]->id)) {
				$app->vars["category"]->parent = null;
				$app("categories")->set($app->vars["category"]->id, $app->vars["category"]);
				$app->page->alerts["parent_loop"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_loop_avoided"));
			}

			if ($app->request->query["category_id"] == "")
				$app->redirectWithMessages("/backend/categories/editor?edit_id=" . $app->vars["category"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}

		$app->vars["title"] = $app("i18n")->getString("edit_category");
	} else {
		$app->vars["title"] = $app("i18n")->getString("new_category");

		if ($formFilled) {
			if ($app->request->query["category_id"] == "")
				$app->vars["category"]->id = $app->nameToId($app->request->query["category_title"]);
			else
				$app->vars["category"]->id = $app->nameToId($app->request->query["category_id"]);

			while ($app("categories")->exists($app->vars["category"]->id)) $app->vars["category"]->id .= "_1";

			$parent = $app->request->query["category_parent"];
			if ($parent == $app->vars["category"]->id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_cannot_be_self"));
			}

			$app->vars["category"]->id = $app->nameToId($app->vars["category"]->id);

			$app->vars["category"]->title =			$app->request->query["category_title"];
			$app->vars["category"]->content =		$app->request->query["category_content"];
			$app->vars["category"]->state =			$app->request->query["category_state"];
			$app->vars["category"]->created =		time();
			$app->vars["category"]->modified =		time();
			$app->vars["category"]->parent =		$parent;
			$app->vars["category"]->tags =			$app->request->query["category_tags"];
			$app->vars["category"]->meta_desc =		($app->request->query["category_meta_desc"] != "") ? $app->request->query["category_meta_desc"] : $app->getSetting("meta_desc", "");
			$app->vars["category"]->meta_keys =		($app->request->query["category_meta_keys"] != "") ? $app->request->query["category_meta_keys"] : $app->getSetting("meta_keys", "");
			$app->vars["category"]->meta_robots =	($app->request->query["category_meta_robots"] != "") ? $app->request->query["category_meta_robots"] : $app->getSetting("meta_robots", "");
			$app->vars["category"]->options =		$app->request->query["category_options"];

			$app("categories")->set($app->vars["category"]->id, $app->vars["category"]);

			$app->redirectWithMessages("/backend/categories/editor?edit_id=" . $app->vars["category"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));
		}
	}

	$app->page->setTitle($app->vars["title"]);

	$app->page->setContent(function($app) {
?>

		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php echo $app->vars["title"]; ?></h1>
					<?php if ($app->vars["category"]->id != "") { ?>
						<a target="_blank" href="<?php echo Config::WEBROOT . "/" . Router::getCategoryRoute($app->vars["category"]->id); ?>"><?php $app("i18n")("view_category"); ?></a>
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
								<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#category_content"><?php $app("i18n")("content"); ?></button>
							</li>
							<li class="nav-item" role="presentation">
								<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#category_options"><?php $app("i18n")("options"); ?></button>
							</li>
						</ul>
						<div class="tab-content">
							<div id="category_content" class="tab-pane show active" role="tabpanel">
								<label for="category_title"><?php $app("i18n")("category_title_c"); ?></label>
								<input type="text" class="form-control" name="category_title" value="<?php echo $app->vars["category"]->title; ?>" required />

								<label for="category_content"><?php $app("i18n")("category_content_c"); ?></label>
								<?php RenderHelper::renderEditor("category_content", htmlentities($app->vars["category"]->content)); ?>
							</div>
							<div id="category_options" class="tab-pane" role="tabpanel">
								<label for="category_options[show_title]"><?php $app("i18n")("show_title_c"); ?></label>
								<?php RenderHelper::renderYesNo("category_options[show_title]", $app->vars["category"]->options["show_title"] ?? $app->getSetting("categories.show_title", "yes")); ?>

								<label for="category_options[show_sidebar]"><?php $app("i18n")("show_sidebar_c"); ?></label>
								<?php RenderHelper::renderYesNo("category_options[show_sidebar]", $app->vars["category"]->options["show_sidebar"] ?? $app->getSetting("categories.show_sidebar", "yes")); ?>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="category_id"><?php $app("i18n")("category_id_c"); ?></label>
						<input type="text" class="form-control" name="category_id" placeholder="auto-generate" value="<?php echo $app->vars["category"]->id; ?>" />

						<label for="category_state"><?php $app("i18n")("category_state_c"); ?></label>
						<select class="form-select" name="category_state">
							<option value="published" <?php if ($app->vars["category"]->state == "published") echo "selected"; ?>><?php $app("i18n")("published"); ?></option>
							<option value="unpublished" <?php if ($app->vars["category"]->state == "unpublished") echo "selected"; ?>><?php $app("i18n")("unpublished"); ?></option>
						</select>

						<label for="category_parent"><?php $app("i18n")("category_parent_c"); ?></label>
						<?php RenderHelper::renderCategoryPicker("category_parent", $app->vars["category"]->parent, array("title" => "[none]", "value" => "")); ?>

						<label for="category_tags"><?php $app("i18n")("category_tags_c"); ?></label>
						<input type="text" class="form-control" name="category_tags" value="<?php echo $app->vars["category"]->tags; ?>" />

						<label for="category_meta_desc"><?php $app("i18n")("meta_description_c"); ?></label>
						<textarea class="form-control" name="category_meta_desc" style="height: 160px;" placeholder="<?php echo $app->getSetting("meta_desc"); ?>"><?php echo $app->vars["category"]->meta_desc; ?></textarea>

						<label for="category_meta_keys"><?php $app("i18n")("meta_keywords_c"); ?></label>
						<input type="text" class="form-control" name="category_meta_keys" placeholder="<?php echo $app->getSetting("meta_keys"); ?>" value="<?php echo $app->vars["category"]->meta_keys; ?>" />

						<label for="category_meta_robots"><?php $app("i18n")("meta_robots_c"); ?></label>
						<input type="text" class="form-control" name="category_meta_robots" placeholder="<?php echo $app->getSetting("meta_robots"); ?>" value="<?php echo $app->vars["category"]->meta_robots; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/categories/list" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("save"); ?></button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.categories.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
