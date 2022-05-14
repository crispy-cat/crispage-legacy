<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/modules/editor.php - Backend module editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$currentUser = Session::getCurrentSession()->user;
	$formFilled = FormHelper::formFieldsFilled(
		"module_title", "module_id", "module_options", "module_pos",
		"module_ord"
	) && is_array($app->request->query["module_options"]);

	$app->vars["module"] = new Module(array());

	if (!User::userHasPermissions($currentUser, UserPermissions::MODIFY_MODULES))
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_modules")));

	if (!isset($app->request->query["class"]))
		$app->redirectWithMessages("/backend/modules/select", array("type" => "info", "content" => $app("i18n")->getString("please_select_module")));

	$app->vars["module_info"] = ExtensionHelper::getModuleInfo($app->request->query["class"]);

	if (!$app->vars["module_info"])
		$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => $app("i18n")->getString("invalid_module_type")));

	if (isset($app->request->query["edit_id"])) {
		if (!$app("modules")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/modules/list", array("type" => "error", "content" => $app("i18n")->getString("module_does_not_exist")));

		$app->vars["module"] = $app("modules")->get($app->request->query["edit_id"]);

		if ($formFilled) {
			$app->vars["module"]->id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["module_id"]) {
				if ($app("modules")->exists($app->request->query["module_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => $app("i18n")->getString("id_taken_using", null, $app->request->query["module_id"], $app->vars["module"]->id));
				} else {
					if ($app->request->query["module_id"] == "")
						$app->vars["module"]->id = $app->nameToId($app->request->query["module_title"]);
					else
						$app->vars["module"]->id = $app->nameToId($app->request->query["module_id"]);

					$app("modules")->delete($app->request->query["edit_id"]);
				}
			}

			$options = array();
			foreach ($app->vars["module_info"]["options"] as $opt)
				$options[$opt["name"]] = $app->request->query["module_options"][$opt["name"]];

			$app->vars["module"]->title		= $app->request->query["module_title"];
			$app->vars["module"]->id			= $app->vars["module"]->id;
			$app->vars["module"]->pos		= $app->request->query["module_pos"];
			$app->vars["module"]->ord		= $app->request->query["module_ord"];
			$app->vars["module"]->modified	= time();
			$app->vars["module"]->options	= $options;

			$app("modules")->set($app->vars["module"]->id, $app->vars["module"]);

			if ($app->request->query["module_id"] == "")
				$app->redirectWithMessages("/backend/modules/editor?class=" . $app->vars["module"]->class . "&edit_id=" . $app->vars["module"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}

		$app->vars["title"] = $app("i18n")->getString("edit_module");
	} else {
		$app->vars["title"] = $app("i18n")->getString("new_module");

		if ($formFilled) {
			if ($app->request->query["module_id"] == "")
				$app->vars["module"]->id = $app->nameToId($app->request->query["module_title"]);
			else
				$app->vars["module"]->id = $app->nameToId($app->request->query["module_id"]);

			while ($app("modules")->exists($app->vars["module"]->id)) $app->vars["module"]->id .= "_1";

			$options = array();
			foreach ($app->vars["module_info"]["options"] as $opt)
				$options[$opt["name"]] = $app->request->query["module_options"][$opt["name"]];

			$app->vars["module"]->title =	$app->request->query["module_title"];
			$app->vars["module"]->class =	$app->request->query["class"];
			$app->vars["module"]->pos =		$app->request->query["module_pos"];
			$app->vars["module"]->ord =		$app->request->query["module_ord"];
			$app->vars["module"]->created =	time();
			$app->vars["module"]->modified =time();
			$app->vars["module"]->options =	$options;

			$app("modules")->set($app->vars["module"]->id, $app->vars["module"]);

			$app->redirectWithMessages("/backend/modules/editor?class=" . $app->request->query["class"] . "&edit_id=" . $app->vars["module"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));
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
						<label for="module_title"><?php $app("i18n")("module_title_c"); ?></label>
						<input type="text" class="form-control" name="module_title" value="<?php echo $app->vars["module"]->title; ?>" required />

						<label><?php $app("i18n")("module_type_c"); ?></label>
						<input type="text" class="form-control" value="<?php echo $app->vars["module_info"]["name"]; ?>" disabled />

						<?php
							foreach ($app->vars["module_info"]["options"] as $option) {
								echo "<label for=\"module_options[{$option["name"]}]\">{$option["label"]}:</label>";
								RenderHelper::renderField("module_options[{$option["name"]}]", $option["type"], $app->vars["module"]->options[$option["name"]] ?? null);
							}
						?>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="module_id"><?php $app("i18n")("module_id_c"); ?></label>
						<input type="text" class="form-control" name="module_id" placeholder="auto-generate" value="<?php echo $app->vars["module"]->id; ?>" />

						<label for="module_pos"><?php $app("i18n")("module_position_c"); ?></label>
						<input type="text" class="form-control" name="module_pos" value="<?php echo $app->vars["module"]->pos; ?>" />

						<label for="module_ord"><?php $app("i18n")("module_order_c"); ?></label>
						<input type="number" class="form-control" name="module_ord" value="<?php echo $app->vars["module"]->ord; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/modules/list" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("save"); ?></button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.modules.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
