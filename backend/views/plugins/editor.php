<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/plugins/editor.php - Backend plugin editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_PLUGINS))
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "You do not have permission to modify plugins"));

	function checkQuery() {
		global $app;

		return	isset($app->request->query["plugin_options"]) && is_array($app->request->query["plugin_options"]) &&
				isset($app->request->query["plugin_priority"]);
	}

	$app->vars["plugin_id"]		= "";
	$app->vars["plugin_options"] = array();
	$app->vars["plugin_priority"]	= "";

	if (!isset($app->request->query["class"]))
		$app->redirectWithMessages("/backend/plugins/select", array("type" => "info", "content" => "Please select a plugin type first"));

	$plugininfo = ExtensionHelper::getPluginInfo($app->request->query["class"]);

	if (!$plugininfo)
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "Invalid plugin type"));

	$app->vars["plugin_name"] = $plugininfo["name"];
	$app->vars["plugin_class_options"] = $plugininfo["options"];

	if (isset($app->request->query["edit_id"])) {
		if (!$app("plugins")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "Plugin does not exist"));

		$plugin = $app("plugins")->get($app->request->query["edit_id"]);

		if (checkQuery()) {
			$options = array();
			foreach ($app->vars["plugin_class_options"] as $opt)
				$options[$opt["name"]] = $app->request->query["plugin_options"][$opt["name"]];

			$plugin->priority	= $app->request->query["plugin_priority"];
			$plugin->modified	= time();
			$plugin->options	= $options;

			$app("plugins")->set($plugin->id, $plugin);

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit Plugin";

		$app->vars["plugin_id"]		= $plugin->id;
		$app->vars["plugin_options"] = $plugin->options;
		$app->vars["plugin_priority"]	= $plugin->priority;
	} else {
		$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "No ID specified"));
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
						<label for="plugin_name">Plugin type:</label>
						<input type="text" class="form-control" value="<?php echo $app->vars["plugin_name"]; ?>" disabled />

						<?php
							foreach ($app->vars["plugin_class_options"] as $option) {
								echo "<label for=\"plugin_options[{$option["name"]}]\">{$option["label"]}:</label>";
								RenderHelper::renderField("plugin_options[{$option["name"]}]", $option["type"], $app->vars["plugin_options"][$option["name"]] ?? null);
							}
						?>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="plugin_id">Plugin ID:</label>
						<input type="text" class="form-control" value="<?php echo $app->vars["plugin_id"]; ?>" disabled />

						<label for="plugin_priority">Plugin Priority:</label>
						<input type="text" class="form-control" name="plugin_priority" value="<?php echo $app->vars["plugin_priority"]; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/plugins/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.plugins.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
