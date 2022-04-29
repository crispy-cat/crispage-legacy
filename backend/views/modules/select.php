<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/modules/select.php - Backend module selection page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("New Module");

	$app->vars["infos"] = ExtensionHelper::getAvailableModules();

	$app->page->setContent(function($app) {
?>
	<div class="row">
		<div class="col">
			<h1>New Module</h1>
		</div>
	</div>
	<div class="row">
<?php
		foreach ($app->vars["infos"] as $info) {
?>
			<div class="col col-md-2 col-lg-3 col-xl-4">
				<div class="card mb-3">
					<div class="card-body">
						<h3 class="card-title"><?php echo $info["name"]; ?></h3>
						<p><?php echo $info["description"]; ?></p>
						<a class="btn btn-success" href="<?php echo Config::WEBROOT . "/backend/modules/editor?class=" . $info["class"]; ?>">Select <i class="bi bi-chevron-right"></i></a>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
<?php
	});

	$app->events->trigger("backend.view.modules.select");

	$app->renderPage();
?>
