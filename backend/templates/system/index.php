s<?php
	defined("CRISPAGE") or die("Application must be started from index.php!");

	$app->page->styles["main"] = array("content" => "
		body {
			width: 100%;
			max-width: 800px;
			margin: 20px auto;
			text-align: center;
			font-family: \"Source Sans Pro\", sans-serif;
		}
	");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $app->page->getBrowserTitle(); ?></title>
		<?php $app->page->renderMetas(); ?>
	</head>
	<body>
		<h1><?php echo $app->page->getTitle(); ?></h1>
		<?php $app->page->renderAlerts(); ?>
		<?php $app->page->renderContent(); ?>
		<?php $app->page->renderStyles(); ?>
		<?php $app->page->renderScripts(); ?>
	</body>
</html>
