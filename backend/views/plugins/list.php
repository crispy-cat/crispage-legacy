<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/plugins/list.php - Backend plugin list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$app->page->setTitle($app("i18n")->getString("plugins"));

	\Crispage\Helpers\Paginator::paginationQuery($app->vars);

	$plugins = $app("plugins")->getAllArr(null, "class");

	\Crispage\Helpers\Paginator::paginateNum($app->vars, $plugins, "plugins");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1><?php $app("i18n")("plugins"); ?></h1>
					<span><?php $app("i18n")("show_c"); ?></span>
					<form class="d-flex">
						<select class="form-select ms-2" name="show">
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="60">60</option>
							<option value="120">120</option>
							<option value="240">240</option>
							<option value="480">480</option>
							<option value="all"><?php $app("i18n")("all"); ?></option>
						</select>
						<button class="btn btn-primary ms-2" type="submit"><?php $app("i18n")("go"); ?></button>
					</form>
				</div>
				<div class="col-12 col-md-8 col-xxl-10">
					<div style="float: right;">
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/plugins/activate" style="width: 130px;"><?php $app("i18n")("activate_plugin"); ?></a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/plugins/list?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							\Crispage\Helpers\RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["plugins"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th><?php $app("i18n")("id"); ?></th>
									<th><?php $app("i18n")("class"); ?></th>
									<th><?php $app("i18n")("priority"); ?></th>
									<th><?php $app("i18n")("created"); ?></th>
									<th><?php $app("i18n")("modified"); ?></th>
									<th><?php $app("i18n")("actions"); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["plugins"] as $plugin) { ?>
									<tr>
										<td><code><?php echo $plugin->id; ?></code></td>
										<td><?php echo $plugin->class; ?></td>
										<td><?php echo $plugin->priority; ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $plugin->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $plugin->modified); ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/plugins/editor?class=<?php echo $plugin->class; ?>&edit_id=<?php echo $plugin->id; ?>">
												<i class="bi bi-pencil"></i> <?php $app("i18n")("edit"); ?>
											</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/plugins/deactivate?deactivate_id=<?php echo $plugin->id; ?>">
												<i class="bi bi-dash-circle"></i> <?php $app("i18n")("deactivate"); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p><?php $app("i18n")("no_plugins_match"); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.plugins.list");

	$app->renderPage();
?>
