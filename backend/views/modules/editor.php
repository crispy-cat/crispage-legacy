<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/modules/editor.php - Backend module editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_MODULES))
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => "You do not have permission to modify modules"));

	function checkQuery() {
		global $app;

		return	isset($app->request->query["module_title"]) &&
				isset($app->request->query["module_id"]) &&
				isset($app->request->query["module_options"]) && is_array($app->request->query["module_options"]) &&
				isset($app->request->query["module_pos"]) &&
				isset($app->request->query["module_ord"]);
	}

	$app->vars["module_title"]	= "";
	$app->vars["module_id"]		= "";
	$app->vars["module_options"] = array();
	$app->vars["module_pos"]	= "";
	$app->vars["module_ord"]	= 0;

	if (!isset($app->request->query["class"]))
		$app->redirectWithMessages("/backend/modules/select", array("type" => "info", "content" => "Please select a module type first"));

	$moduleinfo = ExtensionHelper::getModuleInfo($app->request->query["class"]);

	if (!$moduleinfo)
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => "Invalid module type"));

	$app->vars["module_name"] = $moduleinfo["name"];
	$app->vars["module_class_options"] = $moduleinfo["options"];

	if (isset($app->request->query["edit_id"])) {
		if (!$app("modules")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => "Module does not exist"));

		$module = $app("modules")->get($app->request->query["edit_id"]);

		if (checkQuery()) {
			$id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["module_id"]) {
				if ($app("modules")->exists($app->request->query["module_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => "The ID '{$app->request->query["module_id"]}' is taken! Using '$id'.");
				} else {
					if ($app->request->query["module_id"] == "")
						$id = $app->nameToId($app->request->query["module_title"]);
					else
						$id = $app->nameToId($app->request->query["module_id"]);

					$app("modules")->delete($app->request->query["edit_id"]);
				}
			}

			$options = array();
			foreach ($app->vars["module_class_options"] as $opt)
				$options[$opt["name"]] = $app->request->query["module_options"][$opt["name"]];

			$module->title		= $app->request->query["module_title"];
			$module->id			= $id;
			$module->pos		= $app->request->query["module_pos"];
			$module->ord		= $app->request->query["module_ord"];
			$module->modified	= time();
			$module->options	= $options;

			$app("modules")->set($id, $module);

			if ($app->request->query["module_id"] == "")
				$app->redirectWithMessages("/backend/modules/editor?class=$module->class&edit_id=$id", array("type" => "success", "content" => "Changes saved."));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit Module";

		$app->vars["module_title"]	= htmlentities($module->title);
		$app->vars["module_id"]		= $module->id;
		$app->vars["module_options"] = $module->options;
		$app->vars["module_pos"]	= htmlentities($module->pos);
		$app->vars["module_ord"]	= htmlentities($module->ord);
	} else {
		$app->vars["title"] = "New Module";

		if (checkQuery()) {
			if ($app->request->query["module_id"] == "")
				$id = $app->nameToId($app->request->query["module_title"]);
			else
				$id = $app->nameToId($app->request->query["module_id"]);

			while ($app("modules")->exists($id)) $id .= "_1";

			$options = array();
			foreach ($app->vars["module_class_options"] as $opt)
				$options[$opt["name"]] = $app->request->query["module_options"][$opt["name"]];

			$module = new Module(array(
				"id"		=> $id,
				"title"		=> $app->request->query["module_title"],
				"class"		=> $app->request->query["class"],
				"pos"		=> $app->request->query["module_pos"],
				"ord"		=> $app->request->query["module_ord"],
				"created"	=> time(),
				"modified"	=> time(),
				"options"	=> $options
			));

			$app("modules")->set($id, $module);

			$app->redirectWithMessages("/backend/modules/editor?class=" . $app->request->query["class"] . "&edit_id=$id", array("type" => "success", "content" => "Changes saved."));
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
			<form method="post">
				<div class="row">
					<div class="col-12 col-lg-8 pe-lg-2">
						<input type="hidden" name="class" value="<?php echo $app->request->query["class"]; ?>" />
						<?php if (isset($app->request->query["edit_id"])) { ?>
							<input type="hidden" name="edit_id" value="<?php echo $app->request->query["edit_id"]; ?>" />
						<?php } ?>
						<label for="module_title">Module Title:</label>
						<input type="text" class="form-control" name="module_title" value="<?php echo $app->vars["module_title"]; ?>" required />

						<label for="module_name">Module type:</label>
						<input type="text" class="form-control" value="<?php echo $app->vars["module_name"]; ?>" disabled />

						<?php
							foreach ($app->vars["module_class_options"] as $option) {
								echo "<label for=\"module_options[{$option["name"]}]\">{$option["label"]}:</label>";
								RenderHelper::renderField("module_options[{$option["name"]}]", $option["type"], $app->vars["module_options"][$option["name"]] ?? null);
							}
						?>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="module_id">Module ID:</label>
						<input type="text" class="form-control" name="module_id" placeholder="auto-generate" value="<?php echo $app->vars["module_id"]; ?>" />

						<label for="module_pos">Module Position:</label>
						<input type="text" class="form-control" name="module_pos" value="<?php echo $app->vars["module_pos"]; ?>" />

						<label for="module_ord">Module Order:</label>
						<input type="number" class="form-control" name="module_ord" value="<?php echo $app->vars["module_ord"]; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/modules/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.modules.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
