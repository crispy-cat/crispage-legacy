<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/support.php - Backend support page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Crispage Support");

	$app->page->setContent(function($app) {
?>
	<div id="main" class="page-content">
		<h1>Crispage Support</h1>
		<p>Limited support is offered by <a href="mailto:the@crispy.cat">email</a>
		to users of Crispage for things such as database management and site
		migration. Additionally you can
		<a href="https://crispage.crispy.cat/docs">view the documentation</a>
		or <a href="https://github.com/crispy-cat/crispage">the GitHub page</a>.</p>
		<br />

		<h2>Installation Information</h2>
		<p>Please include all of the following information when contacting support.</p>
<pre>
# = Server Information =
#     Document Root:    <?php echo $_SERVER["DOCUMENT_ROOT"]; ?>

#     Server Address:   <?php echo $_SERVER["SERVER_ADDR"]; ?>

#     Server Signature: <?php echo $_SERVER["SERVER_SIGNATURE"]; ?>
#
# = PHP Information =
#     PHP Version:      <?php echo phpversion(); ?>

#     PHP Extensions:   <?php echo implode(", ", get_loaded_extensions()) . "\n"; ?>
#
#
# = Crispage Information =
#     Version:                  <?php echo CRISPAGE; ?>

#     Application Root:         '<?php echo Config::APPROOT; ?>'
#     Web Root:                 '<?php echo Config::WEBROOT; ?>'
#     Error Reporting Level:    <?php echo Config::ERRORLVL; ?>

#     Database Type:            <?php echo Config::DB_TYPE; ?>

#     Database Location:        <?php echo Config::DB_LOC; ?>

#     Database Name:            <?php echo Config::DB_NAME; ?>

#     Database Username:        <?php echo Config::DB_OPTIONS["USERNAME"]; ?>
</pre>
	</div>
<?php
	});

	$app->events->trigger("backend.view.support");

	$app->renderPage();
?>
