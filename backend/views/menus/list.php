<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menus/list.php - Backend menu list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Menus");

	$app->vars["show"] = (is_numeric($app->request->query["show"])) ?  $app->request->query["show"] : 15;
	$app->vars["page"] = (is_numeric($app->request->query["page"])) ? $app->request->query["page"] : 1;

	$menus = $app("menus")->getAllArr(null, "title");

	$app->vars["npages"] = Paginator::numPages($menus, $app->vars["show"]);
	$app->vars["menus"] = Paginator::Paginate($menus, $app->vars["show"], $app->vars["page"]);

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1>Menus</h1>
					<span>Show only:</span>
					<form class="d-flex">
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
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/menus/editor" style="width: 120px;">New Menu</a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/menus/list?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
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
									<th>Id</th>
									<th>Title</th>
									<th>Items</th>
									<th>Created</th>
									<th>Modified</th>
									<th>Actions</th>
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
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/menus/editor?edit_id=<?php echo $menu->id; ?>"><i class="bi bi-pencil"></i> Edit</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/menus/delete?delete_id=<?php echo $menu->id; ?>"><i class="bi bi-trash"></i> Delete</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No menus match your criteria!</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menus.list");

	$app->renderPage();
?>
