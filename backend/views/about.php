<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/about.php - Backend about page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("About Crispage");

	$app->page->setContent(function($app) {
?>
	<h1>About Crispage</h1>
	<p>Crispage is made for developers who want a CMS without the bulky overhead of traditional options.</p>
	<br />

	<h2>Version Information</h2>
	<p>You are currently using Crispage version <?php echo CRISPAGE; ?>.</p>
	<p><a class="btn btn-success btn-lg">Check for updates</a></p>
	<br />

	<h2>License Information</h2>

	<p>This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.</p>

	<p>This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.</p>

	<p>You should have received a copy of the GNU General Public License
	along with this program. If not, see <a href="https://www.gnu.org/licenses/">https://www.gnu.org/licenses/</a>.</p>
<?php
	});

	$app->events->trigger("backend.view.about");

	$app->renderPage();
?>
