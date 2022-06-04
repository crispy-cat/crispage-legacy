<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/plugins/activate.php - Backend plugin activation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.5.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$app->page->setTitle($app("i18n")->getString("activate_plugin"));

	if (isset($app->request->query["activate_id"])) {
		$id = $app->request->query["activate_id"];
		$ext = $app->database->readRow("installation", $id);
		if (!$ext)
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => $app("i18n")->getString("plugin_not_in_installation_table")));
		if (!$app("plugins")->exists($ext["class"])) {
			$plugin = new \Crispage\Assets\Plugin(array(
				"id" => $app->nameToId(basename($ext["class"])),
				"class" => $ext["class"],
				"priority" => 0,
				"scope" => $ext["scope"],
				"created" => time(),
				"modified" => time(),
				"options" => array()
			));

			$app("plugins")->set(basename($ext["class"]), $plugin);
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "success", "content" => $app("i18n")->getString("plugin_activated")));
		} else {
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "warning", "content" => $app("i18n")->getString("plugin_already_activated")));
		}
	}

	$plugins = $app->database->readRows("installation", array("type" => "plugin"));

	$app->vars["plugins"] = array();
	foreach ($plugins as $plugin)
		if (!count($app->database->readRows("plugins", array("class" => $plugin["class"]))))
			$app->vars["plugins"][] = $plugin;

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12">
					<h1><?php $app("i18n")("activate_plugin"); ?></h1>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["plugins"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th><?php $app("i18n")("id"); ?></th>
									<th><?php $app("i18n")("class"); ?></th>
									<th><?php $app("i18n")("scope"); ?></th>
									<th><?php $app("i18n")("actions"); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["plugins"] as $plugin) { ?>
									<tr>
										<td><code><?php echo $plugin["id"]; ?></code></td>
										<td><?php echo $plugin["class"]; ?></td>
										<td><?php echo $plugin["scope"]; ?></td>
										<td>
											<a class="btn btn-success btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/plugins/activate?activate_id=<?php echo $plugin["id"]; ?>">
												<i class="bi bi-plus-circle"></i> <?php $app("i18n")("activate"); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p><?php $app("i18n")("no_plugins_to_activate"); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.plugins.list");

	$app->renderPage();
?>
