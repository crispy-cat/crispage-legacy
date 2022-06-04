<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menus/list.php - Backend menu list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$app->page->setTitle($app("i18n")->getString("menus"));

	\Crispage\Helpers\Paginator::paginationQuery($app->vars);

	$menus = $app("menus")->getAllArr(null, "title");

	\Crispage\Helpers\Paginator::paginateNum($app->vars, $menus, "menus");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1><?php $app("i18n")("menus"); ?></h1>
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
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/menus/editor" style="width: 120px;"><?php $app("i18n")("new_menu"); ?></a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/menus/list?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							\Crispage\Helpers\RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["menus"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th><?php $app("i18n")("id"); ?></th>
									<th><?php $app("i18n")("title"); ?></th>
									<th><?php $app("i18n")("items"); ?></th>
									<th><?php $app("i18n")("created"); ?></th>
									<th><?php $app("i18n")("modified"); ?></th>
									<th><?php $app("i18n")("actions"); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["menus"] as $menu) { ?>
									<tr>
										<td><code><?php echo $menu->id; ?></code></td>
										<td><?php echo htmlentities($menu->title); ?></td>
										<td><?php echo count($app("menu_items")->getAllArr(array("menu" => $menu->id))); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $menu->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $menu->modified); ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/menus/editor?edit_id=<?php echo $menu->id; ?>">
												<i class="bi bi-pencil"></i> <?php $app("i18n")("edit"); ?>
											</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/menus/delete?delete_id=<?php echo $menu->id; ?>">
												<i class="bi bi-trash"></i> <?php $app("i18n")("delete"); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p><?php $app("i18n")("no_menus_match"); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menus.list");

	$app->renderPage();
?>
