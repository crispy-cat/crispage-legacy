<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/extensions/list.php - Extension list installer page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle("Extensions");

	$app->vars["extensions"] = $app->database->readRows("installation");

	$app->page->setContent(function($app) {
?>
		<h1>Extensions</h1>
		<div style="float: right;">
			<a class="btn btn-success" href="<?php echo Config::WEBROOT; ?>/installer/extensions/install">Install Extension Pack</a>
		</div>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Scope</th>
					<th>Type</th>
					<th>Extension</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
<?php
			foreach ($app->vars["extensions"] as $extension) {
?>
				<tr>
					<td><?php echo $extension["id"]; ?></td>
					<td><?php echo $extension["scope"]; ?></td>
					<td><?php echo $extension["type"]; ?></td>
					<td><?php echo $extension["class"]; ?></td>
					<td><a class="btn btn-sm btn-danger" href="<?php echo Config::WEBROOT; ?>/installer/extensions/uninstall?uninstall_id=<?php echo $extension["id"]; ?>">Uninstall</a></td>
				</tr>
<?php
			}
?>
			</tbody>
		</table>
<?php
	});

	$app->renderPage();
?>
