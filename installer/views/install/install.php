<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/install/install.php - Installer initial installation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.2
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	define("IS_INSTALL_PAGE", true);
	require_once \Config::APPROOT . "/installer/header.php";

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

	<form action="<?php echo \Config::WEBROOT; ?>/installer/script/package" method="post">
		<input type="hidden" name="name" value="default" />

		<label for="iopts[approot]">Application Files Root:</label>
		<input type="text" class="form-control" name="iopts[approot]" value="<?php echo \Config::APPROOT; ?>" required />

		<label for="iopts[webroot]">URL Root:</label>
		<input type="text" class="form-control" name="iopts[webroot]" value="<?php echo \Config::WEBROOT; ?>" />

		<hr />

		<label for="iopts[db_type]">Database Type:</label>
		<select class="form-control" name="iopts[db_type]">
			<option value="\Crispage\Database\JSONDatabase">JSON</option>
			<option value="\Crispage\Database\SQLiteDatabase">SQLite3</option>
			<option value="\Crispage\Database\MySQLDatabase">MySQL</option>
		</select>

		<label for="iopts[db_loc]">Database Location or Host:</label>
		<input type="text" class="form-control" name="iopts[db_loc]" value="<?php echo \Config::APPROOT . "/database"; ?>" required />

		<label for="iopts[db_name]">Database Name:</label>
		<input type="text" class="form-control" name="iopts[db_name]" required />

		<label for="iopts[db_user]">Database Username (Optional):</label>
		<input type="text" class="form-control" name="iopts[db_user]" />

		<label for="iopts[db_pass]">Database Password (Optional):</label>
		<input type="password" class="form-control" name="iopts[db_pass]" />

		<label for="iopts[password_table]">Password Table:</label>
		<input type="text" class="form-control" name="iopts[password_table]" value="<?php echo "auth_" . \Crispage\Helpers\Randomizer::randomString(8, 16); ?>" required />

		<hr />

		<label for="iopts[sitename]">Site Name:</label>
		<input type="text" class="form-control" name="iopts[sitename]" required />

		<label for="iopts[sitedesc]">Site Description:</label>
		<input type="text" class="form-control" name="iopts[sitedesc]" required />

		<label for="iopts[language]">Site Language:</label>
		<input type="text" class="form-control" name="iopts[language]" value="en-US" required />

		<label for="iopts[charset]">Site Charset:</label>
		<input type="text" class="form-control" name="iopts[charset]" value="UTF-8" required />

		<hr />

		<label for="iopts[timezone]">Site Timezone:</label>
		<input type="text" class="form-control" name="iopts[timezone]" value="America/New_York" required />

		<label for="iopts[date_format]">Date Format:</label>
		<input type="text" class="form-control" name="iopts[date_format]" value="Y-m-d" required />

		<label for="iopts[time_format]">Time Format:</label>
		<input type="text" class="form-control" name="iopts[time_format]" value="H:i" required />

		<label for="iopts[date_format_long]">Long Date Format:</label>
		<input type="text" class="form-control" name="iopts[date_format_long]" value="Y, F j" required />

		<label for="iopts[time_format_long]">Long Time Format:</label>
		<input type="text" class="form-control" name="iopts[time_format_long]" value="H:i:s" required />

		<hr />

		<label for="iopts[super_user_name]">Super User Name:</label>
		<input type="text" class="form-control" name="iopts[super_user_name]" required />

		<label for="iopts[super_user_id]">Super User ID:</label>
		<input type="text" class="form-control" name="iopts[super_user_id]" required />

		<label for="iopts[super_user_email]">Super User Email:</label>
		<input type="email" class="form-control" name="iopts[super_user_email]" required />

		<label for="iopts[super_user_password]">Super User Password:</label>
		<input type="password" class="form-control" name="iopts[super_user_password]" required />

		<hr />

		<input type="submit" class="btn btn-success" value="Install Crispage" />
	</form>
<?php
	});

	$app->renderPage();
?>
