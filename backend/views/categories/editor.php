<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/categories/editor.php - Backend category editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_CATEGORIES))
		$app->redirect(Config::WEBROOT . "/backend/categories/list?me=You do not have permission to modify categories");

	function checkQuery() {
		global $app;

		return	isset($app->request->query["category_title"]) &&
				isset($app->request->query["category_content"]) &&
				isset($app->request->query["category_id"]) &&
				isset($app->request->query["category_state"]) &&
				isset($app->request->query["category_parent"]) &&
				isset($app->request->query["category_tags"]) &&
				isset($app->request->query["category_meta_desc"]) &&
				isset($app->request->query["category_meta_keys"]) &&
				isset($app->request->query["category_meta_robots"]) &&
				isset($app->request->query["category_options"]) &&
				is_array($app->request->query["category_options"]);
	}

	$app->vars["category_title"]		= "";
	$app->vars["category_content"]		= "";
	$app->vars["category_id"]			= "";
	$app->vars["category_published"]	= true;
	$app->vars["category_parent"]		= null;
	$app->vars["category_tags"]			= "";
	$app->vars["category_meta_desc"]	= "";
	$app->vars["category_meta_keys"]	= "";
	$app->vars["category_meta_robots"]	= "";
	$app->vars["category_options"]		= array();

	if (isset($app->request->query["edit_id"])) {
		if (!$app->content->existsCategory($app->request->query["edit_id"]))
			$app->redirect(Config::WEBROOT . "/backend/categories/list?me=Category does not exist");

		$category = $app->content->getCategory($app->request->query["edit_id"]);

		if (checkQuery()) {
			$id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["category_id"]) {
				if ($app->content->existsCategory($app->request->query["category_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => "The ID '{$app->request->query["category_id"]}' is taken! Using '$id'.");
				} else {
					if ($app->request->query["category_id"] == "")
						$id = $app->nameToId($app->request->query["category_title"]);
					else
						$id = $app->nameToId($app->request->query["category_id"]);

					$app->content->deleteCategory($app->request->query["edit_id"]);
				}
			}

			$parent = $app->request->query["category_parent"];
			if ($parent == $id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => "Parent category cannot be self.");
			}

			$category->title	= $app->request->query["category_title"];
			$category->content	= $app->request->query["category_content"];
			$category->id		= $id;
			$category->state	= $app->request->query["category_state"];
			$category->modified	= time();
			$category->parent	= $parent;
			$category->tags		= $app->request->query["category_tags"];
			$category->meta_desc= ($app->request->query["category_meta_desc"] != "") ? $app->request->query["category_meta_desc"] : $app->getSetting("meta_keys", "");
			$category->meta_keys= ($app->request->query["category_meta_keys"] != "") ? $app->request->query["category_meta_keys"] : $app->getSetting("meta_keys", "");
			$category->meta_robots= ($app->request->query["category_meta_robots"] != "") ? $app->request->query["category_meta_robots"] : $app->getSetting("meta_robots", "");
			$category->options	= $app->request->query["category_options"];

			$app->content->setCategory($id, $category);

			if ($app->content->categoryParentLoop($id)) {
				$category->parent = null;
				$app->content->setCategory($id, $category);
				$app->page->alerts["parent_loop"] = array("class" => "warning", "content" => "Parent category cannot cause an infinite loop.");
			}

			if ($app->request->query["category_id"] == "")
				$app->redirect(Config::WEBROOT . "/backend/categories/editor?edit_id=$id&ms=Changes saved.");

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit Category";

		$app->vars["category_title"]	= htmlentities($category->title);
		$app->vars["category_content"]	= htmlentities($category->content);
		$app->vars["category_id"]		= $category->id;
		$app->vars["category_published"]= $category->state == "published";
		$app->vars["category_parent"]	= htmlentities($category->parent);
		$app->vars["category_tags"]		= htmlentities($category->tags);
		$app->vars["category_meta_desc"]= htmlentities($category->meta_desc);
		$app->vars["category_meta_keys"]= htmlentities($category->meta_keys);
		$app->vars["category_meta_robots"]= htmlentities($category->meta_robots);
		$app->vars["category_options"]	= $category->options;
	} else {
		$app->vars["title"] = "New Category";

		if (checkQuery()) {
			if ($app->request->query["category_id"] == "")
				$id = $app->nameToId($app->request->query["category_title"]);
			else
				$id = $app->nameToId($app->request->query["category_id"]);

			while ($app->content->existsCategory($id)) $id .= "_1";

			$parent = $app->request->query["category_parent"];
			if ($parent == $id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => "Parent category cannot be self.");
			}
			// TODO: Check for loop

			$id = $app->nameToId($id);

			$category = new Category(array(
				"id"		=> $id,
				"title"		=> $app->request->query["category_title"],
				"content"	=> $app->request->query["category_content"],
				"state"		=> $app->request->query["category_state"],
				"created"	=> time(),
				"modified"	=> time(),
				"parent"	=> $parent,
				"tags"		=> $app->request->query["category_tags"],
				"meta_desc" => ($app->request->query["category_meta_desc"] != "") ? $app->request->query["category_meta_desc"] : $app->getSetting("meta_desc", ""),
				"meta_keys" => ($app->request->query["category_meta_keys"] != "") ? $app->request->query["category_meta_keys"] : $app->getSetting("meta_keys", ""),
				"meta_robots" => ($app->request->query["category_meta_robots"] != "") ? $app->request->query["category_meta_robots"] : $app->getSetting("meta_robots", ""),
				"options"	=> $app->request->query["category_options"]
			));

			$app->content->setCategory($id, $category);

			$app->redirect(Config::WEBROOT . "/backend/categorys/editor?edit_id=$id&ms=Changes saved.");
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
					<input type="hidden" name="category_options[]" value="" />
					<div class="col col-lg-8 me-lg-2">
						<label for="category_title">Category Title:</label>
						<input type="text" class="form-control" name="category_title" value="<?php echo $app->vars["category_title"]; ?>" required />

						<label for="category_content">Category Content:</label>
						<textarea class="form-control" name="category_content" style="height: 300px; font-family: monospace;" required onkeydown="if(event.keyCode===9){var v=this.value,s=this.selectionStart,e=this.selectionEnd;this.value=v.substring(0, s)+'\t'+v.substring(e);this.selectionStart=this.selectionEnd=s+1;return false;}"><?php echo $app->vars["category_content"]; ?></textarea>
					</div>
					<div class="col col-lg-4 ms-lg-2">
						<label for="category_id">Category ID:</label>
						<input type="text" class="form-control" name="category_id" placeholder="auto-generate" value="<?php echo $app->vars["category_id"]; ?>" />

						<label for="category_state">Category Status:</label>
						<select class="form-select" name="category_state">
							<option value="published" <?php if ($app->vars["category_published"]) echo "selected"; ?>>Published</option>
							<option value="unpublished" <?php if (!$app->vars["category_published"]) echo "selected"; ?>>Unpublished</option>
						</select>

						<label for="category_parent">Parent:</label>
						<?php RenderHelper::renderCategoryPicker("category_parent", $app->vars["category_parent"], array("title" => "[none]", "value" => "")); ?>

						<label for="category_tags">Category Tags:</label>
						<input type="text" class="form-control" name="category_tags" value="<?php echo $app->vars["category_tags"]; ?>" />

						<label for="category_meta_desc">Category Description:</label>
						<textarea class="form-control" name="category_meta_desc" style="height: 160px;" placeholder="<?php echo $app->getSetting("meta_desc"); ?>"><?php echo $app->vars["category_meta_desc"]; ?></textarea>

						<label for="category_meta_keys">Category Keywords:</label>
						<input type="text" class="form-control" name="category_meta_keys" placeholder="<?php echo $app->getSetting("meta_keys"); ?>" value="<?php echo $app->vars["category_meta_keys"]; ?>" />

						<label for="category_meta_robots">Category Meta Robots:</label>
						<input type="text" class="form-control" name="category_meta_robots" placeholder="<?php echo $app->getSetting("meta_robots"); ?>" value="<?php echo $app->vars["category_meta_robots"]; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 me-2" href="<?php echo Config::WEBROOT; ?>/backend/categories/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</form>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.categories.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
