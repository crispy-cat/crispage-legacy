<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menu_items/list.php - Backend menu item list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Menu Items");

	$app->vars["menu"] = $app->request->query["menu"] ?? null;
	$app->vars["show"] = $app->request->query["show"] ?? 15;
	$app->vars["page"] = $app->request->query["page"] ?? 1;

	$items = $app->menus->getMenuItems($app->vars["menu"]);

	$app->vars["npages"] = Paginator::numPages($items, (is_numeric($app->vars["show"])) ? $app->vars["show"] : 0);

	$app->vars["items"] = Paginator::sPaginate($items, $app->vars["show"], $app->vars["page"]);

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1>Menu Items</h1>
					<span>Show only:</span>
					<form class="d-flex">
						<?php RenderHelper::renderMenuPicker("menu", null, array("title" => "All Menus", "value" => "")); ?>
						<select class="form-select ms-2" name="show">
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="60">60</option>
							<option value="120">120</option>
							<option value="240">240</option>
							<option value="480">480</option>
							<option value="all">All</option>
						</select>
						<button class="btn btn-primary ms-2" type="submit">Go</button>
					</form>
				</div>
				<div class="col-12 col-md-8 col-xxl-10">
					<div style="float: right;">
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/editor" style="width: 130px;">New Menu Item</a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/articles/list?menu=" . (($app->vars["menu"]) ? $app->vars["menu"] : "") . "&show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["items"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Id</th>
									<th>Label</th>
									<th>Type</th>
									<th>Menu</th>
									<th>Parent</th>
									<th>Order</th>
									<th>Created</th>
									<th>Modified</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["items"] as $item) { ?>
									<tr>
										<td><code><?php echo $item->id; ?></code></td>
										<td><?php echo htmlentities($item->label); ?></td>
										<td><?php echo htmlentities($item->type); ?></td>
										<td><?php echo @htmlentities($app->menus->getMenu($item->menu)->title); ?></td>
										<td><?php echo @htmlentities($app->menus->getMenuItem($item->parent)->label); ?></td>
										<td><?php echo htmlentities($item->ord); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $item->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $item->modified); ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/editor?edit_id=<?php echo $item->id; ?>"><i class="bi bi-pencil"></i> Edit</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/delete?delete_id=<?php echo $item->id; ?>"><i class="bi bi-trash"></i> Delete</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No items match your criteria!</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menu_items.list");

	$app->renderPage();
?>
