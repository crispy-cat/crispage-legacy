<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/plugins/activate.php - Backend plugin activation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.5.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Activate Plugins");

	if (isset($app->request->query["activate_id"])) {
		$id = $app->request->query["activate_id"];
		$ext = $app->database->readRow("installation", $id);
		if (!$ext)
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "error", "content" => "Plugin does not exist in installation table"));
		if (!$app->plugins->existsPlugin($ext["class"])) {
			$plugin = new Plugin(array(
				"id" => basename($ext["class"]),
				"class" => $ext["class"],
				"priority" => 0,
				"scope" => $ext["scope"],
				"created" => time(),
				"modified" => time(),
				"options" => array()
			));

			$app->plugins->setPlugin(basename($ext["class"]), $plugin);
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "success", "content" => "Plugin activated"));
		} else {
			$app->redirectWithMessages("/backend/plugins/list", array("type" => "warning", "content" => "Plugin already activated"));
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
					<h1>Activate Plugins</h1>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["plugins"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Id</th>
									<th>Class</th>
									<th>Scope</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["plugins"] as $plugin) { ?>
									<tr>
										<td><code><?php echo $plugin["id"]; ?></code></td>
										<td><?php echo $plugin["class"]; ?></td>
										<td><?php echo $plugin["scope"]; ?></td>
										<td>
											<a class="btn btn-success btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/plugins/activate?activate_id=<?php echo $plugin["id"]; ?>"><i class="bi bi-plus-circle"></i> Activate</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No plugins found.</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.plugins.list");

	$app->renderPage();
?>
