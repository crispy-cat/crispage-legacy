<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/settings.php - Backend settings page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	if (isset($app->request->query["settings"]) && is_array($app->request->query["settings"])) {
		if (!\Crispage\Assets\User::userHasPermissions(\Crispage\Assets\Session::getCurrentSession()->user, \Crispage\Users\UserPermissions::MODIFY_SETTINGS))
			$app->redirectWithMessages("/backend/settings", array("type" => "error", "content" => $app("i18n")->getString("no_permission_settings")));

		foreach ($app->request->query["settings"] as $setting => $value)
			$app->setSetting($setting, $value);

		$app->page->alerts["settings_updated"] = array("class" => "success", "content" => $app("i18n")->getString("settings_updated"));
	}

	$app->page->setTitle($app("i18n")->getString("settings"));

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php $app("i18n")("settings"); ?></h1>
					<p><?php $app("i18n")("use_this_settings"); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#settings_site"><?php $app("i18n")("site"); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_seo"><?php $app("i18n")("seo"); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_appearance"><?php $app("i18n")("appearance"); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_articles"><?php $app("i18n")("articles"); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_categories"><?php $app("i18n")("categories"); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_modules"><?php $app("i18n")("modules"); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_users"><?php $app("i18n")("users"); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_mail"><?php $app("i18n")("mail"); ?></button>
						</li>
					</ul>
					<form class="form" method="post">
						<div class="tab-content">
							<div id="settings_site" class="tab-pane show active" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>General</h2>
										<label for="settings[sitename]">Site Name:</label>
										<input type="text" class="form-control" name="settings[sitename]" value="<?php echo htmlentities($app->getSetting("sitename", "Crispage Site")); ?>" />
										<label for="settings[site_desc]">Site Description:</label>
										<input type="text" class="form-control" name="settings[site_desc]" value="<?php echo htmlentities($app->getSetting("site_desc", "Powered by Crispage")); ?>" />
										<label for="settings[charset]">Character Set:</label>
										<input type="text" class="form-control" name="settings[charset]" value="<?php echo htmlentities($app->getSetting("charset", "UTF-8")); ?>" />
										<label for="settings[language]">Site Language:</label>
										<input type="text" class="form-control" name="settings[language]" value="<?php echo htmlentities($app->getSetting("language", "en-US")); ?>" />
										<label for="settings[date_format]">Short Date Format:</label>
										<input type="text" class="form-control" name="settings[date_format]" value="<?php echo htmlentities($app->getSetting("date_format", "Y-m-d")); ?>" />
										<label for="settings[date_format_long]">Long Date Format:</label>
										<input type="text" class="form-control" name="settings[date_format_long]" value="<?php echo htmlentities($app->getSetting("date_format_long", "Y, F j")); ?>" />
										<label for="settings[time_format]">Short Time Format:</label>
										<input type="text" class="form-control" name="settings[time_format]" value="<?php echo htmlentities($app->getSetting("time_format", "H:i")); ?>" />
										<label for="settings[time_format_long]">Long Time Format:</label>
										<input type="text" class="form-control" name="settings[time_format_long]" value="<?php echo htmlentities($app->getSetting("time_format_long", "H:i:s")); ?>" />
									</div>
								</div>
							</div>
							<div id="settings_seo" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>SEO</h2>
										<label for="settings[meta_desc]">Default Meta Description:</label>
										<textarea class="form-control" name="settings[meta_desc]" required><?php echo htmlentities($app->getSetting("meta_desc", "Crispage")); ?></textarea>
										<label for="settings[meta_keys]">Default Meta Keywords:</label>
										<textarea class="form-control" name="settings[meta_keys]" required><?php echo htmlentities($app->getSetting("meta_keys", "crispage,crispycat")); ?></textarea>
										<label for="settings[meta_robots]">Default Meta Robots:</label>
										<input type="text" class="form-control" name="settings[meta_robots]" value="<?php echo htmlentities($app->getSetting("meta_robots", "index, follow")); ?>" />
									</div>
								</div>
							</div>
							<div id="settings_appearance" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Appearance</h2>
										<label for="settings[logopath]">Site Logo Path:</label>
										<input type="text" class="form-control" name="settings[logopath]" value="<?php echo htmlentities($app->getSetting("logopath", Config::WEBROOT . "/media/crispage.png")); ?>" />
										<label for="settings[title_sep]">Page Title Separator:</label>
										<input type="text" class="form-control" name="settings[title_sep]" value="<?php echo htmlentities($app->getSetting("title_sep", " ‹ ")); ?>" />
										<label for="settings[template]">Site Template:</label>
										<input type="text" class="form-control" name="settings[template]" value="<?php echo htmlentities($app->getSetting("template", "crispy")); ?>" />
										<label for="settings[backend_template]">Backend Template:</label>
										<input type="text" class="form-control" name="settings[backend_template]" value="<?php echo htmlentities($app->getSetting("backend_template", "crispage")); ?>" />
										<label for="settings[colors.primary]">Primary Color:</label>
										<input type="text" class="form-control" name="settings[colors.primary]" value="<?php echo htmlentities($app->getSetting("colors.primary", "#002060")); ?>" />
										<label for="settings[colors.secondary]">Secondary Color:</label>
										<input type="text" class="form-control" name="settings[colors.secondary]" value="<?php echo htmlentities($app->getSetting("colors.secondary", "#0d6efd")); ?>" />
										<label for="settings[icons_location]">Icon Folder Location:</label>
										<input type="text" class="form-control" name="settings[icons_location]" value="<?php echo htmlentities($app->getSetting("icons_location", Config::WEBROOT . "/media/icons")); ?>" />
									</div>
								</div>
							</div>
							<div id="settings_articles" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Articles</h2>

										<label for="settings[articles.show_comments]">Show Comments (Default):</label>
										<select class="form-control" name="settings[articles.show_comments]">
											<?php if ($app->getSetting("articles.show_comments", "yes") == "yes") { ?>
												<option value="yes" selected>Yes</option>
												<option value="no">No</option>
											<?php } else { ?>
												<option value="yes">Yes</option>
												<option value="no" selected>No</option>
											<?php } ?>
										</select>

										<label for="settings[articles.show_info]">Show Info (Default):</label>
										<select class="form-control" name="settings[articles.show_info]">
											<?php if ($app->getSetting("articles.show_info", "yes") == "yes") { ?>
												<option value="yes" selected>Yes</option>
												<option value="no">No</option>
											<?php } else { ?>
												<option value="yes">Yes</option>
												<option value="no" selected>No</option>
											<?php } ?>
										</select>

										<label for="settings[articles.show_title]">Show Title (Default):</label>
										<select class="form-control" name="settings[articles.show_title]">
											<?php if ($app->getSetting("articles.show_title", "yes") == "yes") { ?>
												<option value="yes" selected>Yes</option>
												<option value="no">No</option>
											<?php } else { ?>
												<option value="yes">Yes</option>
												<option value="no" selected>No</option>
											<?php } ?>
										</select>

										<label for="settings[articles.show_sidebar]">Show Sidebar (Default):</label>
										<select class="form-control" name="settings[articles.show_sidebar]">
											<?php if ($app->getSetting("articles.show_sidebar", "yes") == "yes") { ?>
												<option value="yes" selected>Yes</option>
												<option value="no">No</option>
											<?php } else { ?>
												<option value="yes">Yes</option>
												<option value="no" selected>No</option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div id="settings_categories" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Categories</h2>

										<label for="settings[categories.show_title]">Show Title (Default):</label>
										<select class="form-control" name="settings[categories.show_title]">
											<?php if ($app->getSetting("categories.show_title", "yes") == "yes") { ?>
												<option value="yes" selected>Yes</option>
												<option value="no">No</option>
											<?php } else { ?>
												<option value="yes">Yes</option>
												<option value="no" selected>No</option>
											<?php } ?>
										</select>

										<label for="settings[categories.show_sidebar]">Show Sidebar (Default):</label>
										<select class="form-control" name="settings[categories.show_sidebar]">
											<?php if ($app->getSetting("categories.show_sidebar", "yes") == "yes") { ?>
												<option value="yes" selected>Yes</option>
												<option value="no">No</option>
											<?php } else { ?>
												<option value="yes">Yes</option>
												<option value="no" selected>No</option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div id="settings_modules" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Modules</h2>
									</div>
								</div>
							</div>
							<div id="settings_users" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Users</h2>
										<label for="settings[users.default_group]">Default group:</label>
										<?php \Crispage\Helpers\RenderHelper::renderUserGroupPicker("settings[users.default_group]", $app->getSetting("users.default_group", "member")); ?>
										<label for="settings[users.password_min]">Minimum Password Length:</label>
										<input type="number" class="form-control" name="settings[users.password_min]" value="<?php echo htmlentities($app->getSetting("users.password_min", "8")); ?>" />
										<label for="settings[users.password_min_letters]">Minimum Letters in Password:</label>
										<input type="number" class="form-control" name="settings[users.password_min_letters]" value="<?php echo htmlentities($app->getSetting("users.password_min_letters", "2")); ?>" />
										<label for="settings[users.password_min_numbers]">Minimum Numbers in Password:</label>
										<input type="number" class="form-control" name="settings[users.password_min_numbers]" value="<?php echo htmlentities($app->getSetting("users.password_min_numbers", "2")); ?>" />
										<label for="settings[users.password_min_special]">Minimum Special Characters in Password:</label>
										<input type="number" class="form-control" name="settings[users.password_min_special]" value="<?php echo htmlentities($app->getSetting("users.password_min_special", "1")); ?>" />
										<label for="settings[users.session_timeout]">Session Timeout (Seconds):</label>
										<input type="number" class="form-control" name="settings[users.session_timeout]" value="<?php echo htmlentities($app->getSetting("users.session_timeout", "3600")); ?>" />
									</div>
								</div>
							</div>
							<div id="settings_mail" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Mail</h2>
										<label for="settings[mail.hostname]">Mail server:</label>
										<input type="text" class="form-control" name="settings[mail.hostname]" value="<?php echo htmlentities($app->getSetting("mail.hostname", "mail.myserver.com")); ?>" />
										<label for="settings[mail.port]">Mail server port:</label>
										<input type="number" class="form-control" name="settings[mail.port]" value="<?php echo htmlentities($app->getSetting("mail.port", "587")); ?>" />
										<label for="settings[mail.from]">From address:</label>
										<input type="email" class="form-control" name="settings[mail.from]" value="<?php echo htmlentities($app->getSetting("mail.from", "me@example.com")); ?>" />
										<label for="settings[mail.username]">Server username:</label>
										<input type="text" class="form-control" name="settings[mail.username]" value="<?php echo htmlentities($app->getSetting("mail.username")); ?>" />
										<label for="settings[mail.password]">Server password:</label>
										<input type="password" class="form-control" name="settings[mail.password]" value="<?php echo htmlentities($app->getSetting("mail.password")); ?>" />
									</div>
								</div>
							</div>
						</div>
						<input type="submit" class="btn btn-success mt-3" value="<?php $app("i18n")("save_changes"); ?>" />
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.settings");

	$app->renderPage();
?>
