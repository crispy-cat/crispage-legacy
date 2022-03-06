<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/dashboard.php - Backend dashboard page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Dashboard");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row mb-2">
				<div class="col">
					<h1>Crispage</h1>
				</div>
			</div>
			<div class="row">
				<div class="col col-md-6 col-lg-4">
					<div class="card mb-3">
						<div class="card-header">System Information</div>
						<div class="card-body">
							<table class="table">
								<tr>
									<td>Version</td>
									<td>
										<?php echo CRISPAGE; ?>
										<a class="btn btn-success btn-sm d-block" href="<?php echo Config::WEBROOT; ?>/backend/about">Check for updates</a>
									</td>
								</tr>
								<tr>
									<td>Server Hostname</td>
									<td><?php echo $_SERVER["SERVER_NAME"]; ?></td>
								</tr>
								<tr>
									<td>Server Signature</td>
									<td><?php echo $_SERVER["SERVER_SIGNATURE"]; ?></td>
								</tr>
								<tr>
									<td>PHP Version</td>
									<td><?php echo phpversion(); ?></td>
								</tr>
								<tr>
									<td>PHP Extensions</td>
									<td><?php echo implode(", ", get_loaded_extensions()); ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="col col-md-6 col-lg-4">
					<div class="card mb-3">
						<div class="card-header">Statistics</div>
						<div class="card-body">
							<table class="table">
								<tr>
									<td>Articles</td>
									<td><b>
										<?php
											$rows = $app->database->readRows("articles");
											if ($rows !== null) echo count($rows);
										?>
									</b></td>
								</tr>
								<tr>
									<td>Categories</td>
									<td><b>
										<?php
											$rows = $app->database->readRows("categories");
											if ($rows !== null) echo count($rows);
										?>
									</b></td>
								</tr>
								<tr>
									<td>Comments</td>
									<td><b>
										<?php
											$rows = $app->database->readRows("comments");
											if ($rows !== null) echo count($rows);
										?>
									</b></td>
								</tr>
								<tr>
									<td>Users</td>
									<td><b>
										<?php
											$rows = $app->database->readRows("users");
											if ($rows !== null) echo count($rows);
										?>
									</b></td>
								</tr>
								<tr>
									<td>Menus</td>
									<td><b>
										<?php
											$rows = $app->database->readRows("menus");
											if ($rows !== null) echo count($rows);
										?>
									</b></td>
								</tr>
								<tr>
									<td>Modules</td>
									<td><b>
										<?php
											$rows = $app->database->readRows("modules");
											if ($rows !== null) echo count($rows);
										?>
									</b></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="col col-md-6 col-lg-4">
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.dashboard");

	$app->renderPage();
?>
