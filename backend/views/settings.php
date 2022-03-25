<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/settings.php - Backend settings page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (isset($app->request->query["settings"]) && is_array($app->request->query["settings"])) {
		if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_SETTINGS))
			$app->redirect(Config::WEBROOT . "/backend/settings?me=You do not have permission to modify settings");

		foreach ($app->request->query["settings"] as $setting => $value)
			$app->setSetting($setting, $value);

		$app->page->alerts["settings_updated"] = array("class" => "success", "content" => "Settings updated.");
	}

	$app->page->setTitle("Settings");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1>Settings</h1>
					<p>Use this page to adjust your site's settings.</p>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#settings_site">Site</button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_seo">SEO</button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_appearance">Appearance</button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_articles">Articles</button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_categories">Categories</button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_modules">Modules</button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_users">Users</button>
						</li>
						<li class="nav-item" role="presentation">
							<button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#settings_mail">Mail</button>
						</li>
					</ul>
					<form class="form" method="post">
						<div class="tab-content">
							<div id="settings_site" class="tab-pane show active" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>General</h2>
										<label for="settings[sitename]">Site Name:</label>
										<input type="text" class="form-control" name="settings[sitename]" value="<?php echo htmlentities($app->getSetting("sitename", "Crispage Site")); ?>" required />
										<label for="settings[site_desc]">Site Description:</label>
										<input type="text" class="form-control" name="settings[site_desc]" value="<?php echo htmlentities($app->getSetting("site_desc", "Powered by Crispage")); ?>" required />
										<label for="settings[charset]">Character Set:</label>
										<input type="text" class="form-control" name="settings[charset]" value="<?php echo htmlentities($app->getSetting("charset", "UTF-8")); ?>" required />
										<label for="settings[date_format]">Short Date Format:</label>
										<input type="text" class="form-control" name="settings[date_format]" value="<?php echo htmlentities($app->getSetting("date_format", "Y-m-d")); ?>" required />
										<label for="settings[date_format_long]">Long Date Format:</label>
										<input type="text" class="form-control" name="settings[date_format_long]" value="<?php echo htmlentities($app->getSetting("date_format_long", "Y, F j")); ?>" required />
										<label for="settings[time_format]">Short Time Format:</label>
										<input type="text" class="form-control" name="settings[time_format]" value="<?php echo htmlentities($app->getSetting("time_format", "H:i")); ?>" required />
										<label for="settings[time_format_long]">Long Time Format:</label>
										<input type="text" class="form-control" name="settings[time_format_long]" value="<?php echo htmlentities($app->getSetting("time_format_long", "H:i:s")); ?>" required />
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
										<input type="text" class="form-control" name="settings[meta_robots]" value="<?php echo htmlentities($app->getSetting("meta_robots", "index, follow")); ?>" required />
									</div>
								</div>
							</div>
							<div id="settings_appearance" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Appearance</h2>
										<label for="settings[logopath]">Site Logo Path:</label>
										<input type="text" class="form-control" name="settings[logopath]" value="<?php echo htmlentities($app->getSetting("logopath", Config::WEBROOT . "/media/crispage.png")); ?>" required />
										<label for="settings[title_sep]">Page Title Separator:</label>
										<input type="text" class="form-control" name="settings[title_sep]" value="<?php echo htmlentities($app->getSetting("title_sep", " ‹ ")); ?>" required />
										<label for="settings[template]">Site Template:</label>
										<input type="text" class="form-control" name="settings[template]" value="<?php echo htmlentities($app->getSetting("template", "crispy")); ?>" required />
										<label for="settings[backend_template]">Backend Template:</label>
										<input type="text" class="form-control" name="settings[backend_template]" value="<?php echo htmlentities($app->getSetting("backend_template", "crispage")); ?>" required />
										<label for="settings[colors.primary]">Primary Color:</label>
										<input type="text" class="form-control" name="settings[colors.primary]" value="<?php echo htmlentities($app->getSetting("colors.primary", "#002060")); ?>" required />
										<label for="settings[colors.secondary]">Secondary Color:</label>
										<input type="text" class="form-control" name="settings[colors.secondary]" value="<?php echo htmlentities($app->getSetting("colors.secondary", "#0d6efd")); ?>" required />
										<label for="settings[icons_location]">Icon Folder Location:</label>
										<input type="text" class="form-control" name="settings[icons_location]" value="<?php echo htmlentities($app->getSetting("icons_location", Config::WEBROOT . "/media/icons")); ?>" required />
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
										<?php RenderHelper::renderUserGroupPicker("settings[users.default_group]", $app->getSetting("users.default_group", "member")); ?>
										<label for="settings[users.password_min]">Minimum Password Length:</label>
										<input type="number" class="form-control" name="settings[users.password_min]" value="<?php echo htmlentities($app->getSetting("users.password_min", "8")); ?>" required />
										<label for="settings[users.password_min_letters]">Minimum Letters in Password:</label>
										<input type="number" class="form-control" name="settings[users.password_min_letters]" value="<?php echo htmlentities($app->getSetting("users.password_min_letters", "2")); ?>" required />
										<label for="settings[users.password_min_numbers]">Minimum Numbers in Password:</label>
										<input type="number" class="form-control" name="settings[users.password_min_numbers]" value="<?php echo htmlentities($app->getSetting("users.password_min_numbers", "2")); ?>" required />
										<label for="settings[users.password_min_special]">Minimum Special Characters in Password:</label>
										<input type="number" class="form-control" name="settings[users.password_min_special]" value="<?php echo htmlentities($app->getSetting("users.password_min_special", "1")); ?>" required />
										<label for="settings[users.session_timeout]">Session Timeout (Seconds):</label>
										<input type="number" class="form-control" name="settings[users.session_timeout]" value="<?php echo htmlentities($app->getSetting("users.session_timeout", "3600")); ?>" required />
									</div>
								</div>
							</div>
							<div id="settings_mail" class="tab-pane" role="tabpanel">
								<div class="row">
									<div class="col-md-6 col-lg-4">
										<h2>Mail</h2>
										<label for="settings[mail.hostname]">Mail server:</label>
										<input type="text" class="form-control" name="settings[mail.hostname]" value="<?php echo htmlentities($app->getSetting("mail.hostname", "mail.myserver.com")); ?>" required />
										<label for="settings[mail.port]">Mail server port:</label>
										<input type="number" class="form-control" name="settings[mail.port]" value="<?php echo htmlentities($app->getSetting("mail.port", "587")); ?>" required />
										<label for="settings[mail.from]">From address:</label>
										<input type="email" class="form-control" name="settings[mail.from]" value="<?php echo htmlentities($app->getSetting("mail.from", "me@example.com")); ?>" required />
										<label for="settings[mail.username]">Server username:</label>
										<input type="text" class="form-control" name="settings[mail.username]" value="<?php echo htmlentities($app->getSetting("mail.username")); ?>" />
										<label for="settings[mail.password]">Server password:</label>
										<input type="password" class="form-control" name="settings[mail.password]" value="<?php echo htmlentities($app->getSetting("mail.password")); ?>" />
									</div>
								</div>
							</div>
						</div>
						<input type="submit" class="btn btn-success mt-3" value="Save changes" />
					</form>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.settings");

	$app->renderPage();
?>
