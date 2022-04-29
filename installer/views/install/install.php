<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/install/install.php - Installer initial installation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.2
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	define("IS_INSTALL_PAGE", true);
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle("Install");

	$app->page->setContent(function($app) {
?>
	<h1>Install Crispage</h1>
	<p>Pre-installation checks:</p>
	<table class="table">
		<thead>
			<tr>
				<th>Requirement</th>
				<th>Required Value</th>
				<th>Actual Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>PHP Version</td>
				<td>&gt;= 7.4</td>
				<td><?php echo phpversion(); ?></td>
			</tr>
			<tr>
				<td>OpenSSL Extension</td>
				<td>enabled</td>
				<td><?php echo (in_array("openssl", get_loaded_extensions())) ? "en" : "dis";?>abled</td>
			</tr>
			<tr>
				<td>Zip Extension</td>
				<td>enabled</td>
				<td><?php echo (in_array("zip", get_loaded_extensions())) ? "en" : "dis";?>abled</td>
			</tr>
		</tbody>
	</table>

	<hr />

	<p>Please fill in the information needed below. If a field is already filled in you may not need to change it.</p>

	<form action="<?php echo Config::WEBROOT; ?>/installer/install/run_installation" method="post">
		<label for="approot">Application Files Root:</label>
		<input type="text" class="form-control" name="approot" value="<?php echo Config::APPROOT; ?>" required />

		<label for="webroot">URL Root:</label>
		<input type="text" class="form-control" name="webroot" value="<?php echo Config::WEBROOT; ?>" />

		<hr />

		<label for="password_table">Password Table:</label>
		<input type="text" class="form-control" name="password_table" value="<?php echo "auth_" . Randomizer::randomString(8, 16); ?>" required />

		<label for="db_loc">Database Files Location:</label>
		<input type="text" class="form-control" name="db_json_loc" value="<?php echo Config::APPROOT . "/database"; ?>" required />

		<label for="db_name">Database Name:</label>
		<input type="text" class="form-control" name="db_json_name" required />

		<hr />

		<label for="sitename">Site Name:</label>
		<input type="text" class="form-control" name="sitename" required />

		<label for="sitedesc">Site Description:</label>
		<input type="text" class="form-control" name="sitedesc" required />

		<label for="charset">Site Charset:</label>
		<input type="text" class="form-control" name="charset" value="UTF-8" required />

		<hr />

		<label for="timezone">Site Timezone:</label>
		<input type="text" class="form-control" name="timezone" value="America/New_York" required />

		<label for="date_format">Date Format:</label>
		<input type="text" class="form-control" name="date_format" value="Y-m-d" required />

		<label for="time_format">Time Format:</label>
		<input type="text" class="form-control" name="time_format" value="H:i" required />

		<label for="date_format_long">Long Date Format:</label>
		<input type="text" class="form-control" name="date_format_long" value="Y, F j" required />

		<label for="time_format_long">Long Time Format:</label>
		<input type="text" class="form-control" name="time_format_long" value="H:i:s" required />

		<hr />

		<label for="super_user_name">Super User Name:</label>
		<input type="text" class="form-control" name="super_user_name" required />

		<label for="super_user_id">Super User ID:</label>
		<input type="text" class="form-control" name="super_user_id" required />

		<label for="super_user_email">Super User Email:</label>
		<input type="email" class="form-control" name="super_user_email" required />

		<label for="super_user_password">Super User Password:</label>
		<input type="password" class="form-control" name="super_user_password" required />

		<hr />

		<input type="submit" class="btn btn-success" value="Install Crispage" />
	</form>
<?php
	});

	$app->renderPage();
?>
