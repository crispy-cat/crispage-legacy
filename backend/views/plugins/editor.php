<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/plugins/editor.php - Backend plugin editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$currentUser = \Crispage\Assets\Session::getCurrentSession()->user;
	$formFilled = \Crispage\Helpers\FormHelper::formFieldsFilled(
		"plugin_options", "plugin_priority"
	) && is_array($app->request->query["plugin_options"]);

	$app->vars["plugin"] = new \Crispage\Assets\Plugin(array());

	if (!\Crispage\Assets\User::userHasPermissions($currentUser, \Crispage\Users\UserPermissions::MODIFY_PLUGINS))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_plugins")));

	if (!isset($app->request->query["class"]))
		$app->redirectWithMessages("/backend/plugins/select", array("type" => "info", "content" => $app("i18n")->getString("invalid_plugin_type")));

	$app->vars["plugin_info"] = \Crispage\Helpers\ExtensionHelper::getPluginInfo($app->request->query["class"]);

	if (!$app->vars["plugin_info"])
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("invalid_plugin_type")));

	if (isset($app->request->query["edit_id"])) {
		if (!$app("plugins")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("plugin_does_not_exist")));

		$app->vars["plugin"] = $app("plugins")->get($app->request->query["edit_id"]);

		if ($formFilled) {
			$options = array();
			foreach ($app->vars["plugin_info"]["options"] as $opt)
				$options[$opt["name"]] = $app->request->query["plugin_options"][$opt["name"]];

			$app->vars["plugin"]->priority	= $app->request->query["plugin_priority"];
			$app->vars["plugin"]->modified	= time();
			$app->vars["plugin"]->options	= $options;

			$app("plugins")->set($app->vars["plugin"]->id, $app->vars["plugin"]);

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}

		$app->vars["title"] = $app("i18n")->getString("edit_plugin");
	} else {
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));
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
						<label><?php $app("i18n")("plugin_type_c"); ?></label>
						<input type="text" class="form-control" value="<?php echo $app->vars["plugin_info"]["name"]; ?>" disabled />

						<?php
							foreach ($app->vars["plugin_info"]["options"] as $option) {
								echo "<label for=\"plugin_options[{$option["name"]}]\">{$option["label"]}:</label>";
								\Crispage\Helpers\RenderHelper::renderField("plugin_options[{$option["name"]}]", $option["type"], $app->vars["plugin"]->options[$option["name"]] ?? null);
							}
						?>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label><?php $app("i18n")("plugin_id_c"); ?></label>
						<input type="text" class="form-control" value="<?php echo $app->vars["plugin"]->id; ?>" disabled />

						<label for="plugin_priority"><?php $app("i18n")("plugin_priority_c"); ?></label>
						<input type="text" class="form-control" name="plugin_priority" value="<?php echo $app->vars["plugin"]->priority; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/plugins/list" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("save"); ?></button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.plugins.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
