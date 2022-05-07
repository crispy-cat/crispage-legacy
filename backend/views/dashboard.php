<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/dashboard.php - Backend dashboard page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle($app("i18n")->getString("dashboard"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row mb-2">
				<div class="col">
					<h1><?php $app("i18n")("crispage"); ?></h1>
				</div>
			</div>
			<div class="row">
				<div class="col col-md-6 col-lg-4">
					<div class="card mb-3">
						<div class="card-header"><?php $app("i18n")("system_information"); ?></div>
						<div class="card-body">
							<table class="table">
								<tr>
									<td><?php $app("i18n")("version"); ?></td>
									<td>
										<?php echo CRISPAGE; ?>
										<a class="btn btn-success btn-sm d-block" href="<?php echo Config::WEBROOT; ?>/backend/about"><?php $app("i18n")("check_for_updates"); ?></a>
									</td>
								</tr>
								<tr>
									<td><?php $app("i18n")("server_hostname"); ?></td>
									<td><?php echo $_SERVER["SERVER_NAME"]; ?></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("server_signature"); ?></td>
									<td><?php echo $_SERVER["SERVER_SIGNATURE"] ?? "[none]"; ?></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("php_version"); ?></td>
									<td><?php echo phpversion(); ?></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("php_extensions"); ?></td>
									<td><?php echo implode(", ", get_loaded_extensions()); ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="col col-md-6 col-lg-4">
					<div class="card mb-3">
						<div class="card-header"><?php $app("i18n")("statistics"); ?></div>
						<div class="card-body">
							<table class="table">
								<tr>
									<td><?php $app("i18n")("articles"); ?></td>
									<td><b><?php echo $app->database->countRows("articles"); ?></b></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("categories"); ?></td>
									<td><b><?php echo $app->database->countRows("categories"); ?></b></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("comments"); ?></td>
									<td><b><?php echo $app->database->countRows("comments"); ?></b></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("users"); ?></td>
									<td><b><?php echo $app->database->countRows("users"); ?></b></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("menus"); ?></td>
									<td><b><?php echo $app->database->countRows("menus"); ?></b></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("modules"); ?></td>
									<td><b><?php echo $app->database->countRows("modules"); ?></b></td>
								</tr>
								<tr>
									<td><?php $app("i18n")("plugins"); ?></td>
									<td><b><?php echo $app->database->countRows("plugins"); ?></b></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="col col-md-6 col-lg-4">
				<div class="card mb-3">
						<div class="card-header"><?php $app("i18n")("online_users"); ?></div>
						<div class="card-body">
							<?php
								$online = array();
								foreach ($app->database->readRows("sessions") as $session) {
									if (
										!in_array($session["user"], $online) &&
										(time() - $session["modified"]) <= $app->getSetting("users.session_max")
									) $online[] = $session["user"];
								}
								
								echo count($online) . " &ndash; " . implode(", ", $online);
							?>	
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.dashboard");

	$app->renderPage();
?>
