<?php
	defined("CRISPAGE") or die("Application must be started from index.php!");

	$logopath = $app->getSetting("logopath", null);
	$uselogo = $logopath != null && $logopath != "";
	$sitename = $app->getSetting("sitename", null);

	$app->page->metas["viewport"] = array("name" => "viewport", "content" => "width=device-width, initial-scale=1");

	$app->page->links["apple-touch-icon"] = array("rel" => "apple-touch-icon", "sizes" => "180x180", "href" => Config::WEBROOT . "/media/icons/apple-touch-icon.png");
	$app->page->links["favicon-16"] = array("rel" => "icon", "type" => "image/png", "sizes" => "16x16", "href" => Config::WEBROOT . "/media/icons/favicon-16x16.png");
	$app->page->links["favicon-32"] = array("rel" => "icon", "type" => "image/png", "sizes" => "32x32", "href" => Config::WEBROOT . "/media/icons/favicon-32x32.png");
	$app->page->links["favicon-194"] = array("rel" => "icon", "type" => "image/png", "sizes" => "194x194", "href" => Config::WEBROOT . "/media/icons/favicon-194x194.png");
	$app->page->links["android-chrome-icon"] = array("rel" => "icon", "type" => "image/png", "sizes" => "192x192", "href" => Config::WEBROOT . "/media/icons/android-chrome-192x192.png");
	$app->page->links["shortcut-icon"] = array("rel" => "shortcut icon","href" => Config::WEBROOT . "/media/icons/favicon.ico");

	$app->page->styles["bootstrap"] = array("content" => file_get_contents(__DIR__ . "/css/bootstrap.min.css"));
	$app->page->styles["fonts"] = array("content" => file_get_contents(__DIR__ . "/css/fonts.css"));
	$app->page->styles["bs-icons"] = array("content" => file_get_contents(__DIR__ . "/css/bootstrap-icons.css"));
	$app->page->styles["template"] = array("content" => file_get_contents(__DIR__ . "/css/template.css"));

	$app->page->scripts["jquery"] = array("content" => file_get_contents(__DIR__ . "/js/jquery.min.js"), "defer" => "");
	$app->page->scripts["bootstrap"] = array("content" => file_get_contents(__DIR__ . "/js/bootstrap.bundle.min.js"), "defer" => "");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $app->page->getBrowserTitle(); ?></title>
		<?php $app->page->renderMetas(); ?>
		<?php $app->page->renderLinks(); ?>
		<?php $app->page->renderStyles(); ?>
		<?php $app->page->renderScripts(); ?>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark bg-crispycat mb-3">
			<div class="container-fluid">
				<a class="navbar-brand" href="<?php echo Config::WEBROOT; ?>/backend"><?php echo ($uselogo) ? "<img src=\"$logopath\" alt=\"$sitename\" height=\"30\" />" : $sitename; ?></a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbar">
					<ul class="navbar-nav me-auto">
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/backend/dashboard"><i class="bi bi-clipboard-data"></i> Dashboard</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-file-richtext"></i> Content
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/articles/list"><i class="bi bi-files"></i> Articles</a></li>
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/categories/list"><i class="bi bi-folder"></i> Categories</a></li>
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/media"><i class="bi bi-images"></i> Media</a></li>
							</ul>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/backend/comments/list"><i class="bi bi-chat-left-dots"></i> Comments</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-menu-app"></i> Menus
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/menus/list"><i class="bi bi-menu-app"></i> Menus</a></li>
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/list"><i class="bi bi-three-dots-vertical"></i> Menu Items</a></li>
							</ul>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/backend/modules/list"><i class="bi bi-grid-1x2"></i> Modules</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-people"></i> Users
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/users/list"><i class="bi bi-person"></i> Manage Users</a></li>
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/list"><i class="bi bi-people"></i> Manage Usergroups</a></li>
							</ul>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/backend/settings"><i class="bi bi-sliders"></i> Settings</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-question-circle"></i> Help
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/about"><i class="bi bi-info-circle"></i> About</a></li>
								<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/support"><i class="bi bi-life-preserver"></i> Support</a></li>
							</ul>
						</li>
					</ul>
					<ul class="navbar-nav ms-auto">
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/" target="_blank"><i class="bi bi-display"></i> View Site</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/logout"><i class="bi bi-door-open"></i> Logout</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container">
			<div class="row">
				<div class="col">
					<?php $app->page->renderAlerts(); ?>
					<?php $app->page->renderContent(); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr />
					<small class="text-muted">Crispage v<?php echo CRISPAGE; ?> &bull; This software is licensed under the GNU GPL v3 License.</small>
				</div>
			</div>
		</div>
	</body>
</html>
