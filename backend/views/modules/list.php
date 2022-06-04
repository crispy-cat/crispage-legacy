<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/modules/list.php - Backend module list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$app->page->setTitle($app("i18n")->getString("modules"));

	\Crispage\Helpers\Paginator::paginationQuery($app->vars);

	$modules = $app("modules")->getAllArr(null, "pos");

	\Crispage\Helpers\Paginator::paginateNum($app->vars, $modules, "modules");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1><?php $app("i18n")("modules"); ?></h1>
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
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/modules/select" style="width: 120px;"><?php $app("i18n")("new_module"); ?></a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/modules/list?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							\Crispage\Helpers\RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["modules"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th><?php $app("i18n")("id"); ?></th>
									<th><?php $app("i18n")("title"); ?></th>
									<th><?php $app("i18n")("position"); ?></th>
									<th><?php $app("i18n")("created"); ?></th>
									<th><?php $app("i18n")("modified"); ?></th>
									<th><?php $app("i18n")("actions"); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["modules"] as $module) { ?>
									<tr>
										<td><code><?php echo $module->id; ?></code></td>
										<td><?php echo htmlentities($module->title); ?></td>
										<td><?php echo htmlentities($module->pos); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $module->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $module->modified); ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/modules/editor?class=<?php echo $module->class; ?>&edit_id=<?php echo $module->id; ?>">
												<i class="bi bi-pencil"></i> <?php $app("i18n")("edit"); ?>
											</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/modules/delete?delete_id=<?php echo $module->id; ?>">
												<i class="bi bi-trash"></i> <?php $app("i18n")("delete"); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p><?php $app("i18n")("no_modules_match"); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.modules.list");

	$app->renderPage();
?>
