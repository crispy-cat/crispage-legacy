<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/usergroups/list.php - Backend user group list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Usergroups");

	$app->vars["show"] = $app->request->query["show"] ?? 15;
	$app->vars["page"] = $app->request->query["page"] ?? 1;

	$groups = $app->users->getUserGroups();

	$app->vars["npages"] = Paginator::numPages($groups, (is_numeric($app->vars["show"])) ? $app->vars["show"] : 0);

	$app->vars["groups"] = Paginator::sPaginate($groups, $app->vars["show"], $app->vars["page"]);

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1>Usergroups</h1>
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
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/editor" style="width: 130px;">New Usergroup</a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/usergroups/list?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["groups"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Id</th>
									<th>Rank</th>
									<th>Name</th>
									<th>Parent</th>
									<th>Created</th>
									<th>Modified</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["groups"] as $group) { ?>
									<tr>
										<td><code><?php echo $group->id; ?></code></td>
										<td><?php echo htmlentities($group->rank); ?></td>
										<td><?php echo htmlentities($group->name); ?></td>
										<td><?php echo ($app->users->getUserGroup($group->parent)) ? htmlentities($app->users->GetUserGroup($group->parent)->name) : "none"; ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $group->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $group->modified); ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/editor?edit_id=<?php echo $group->id; ?>"><i class="bi bi-pencil"></i> Edit</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/delete?delete_id=<?php echo $group->id; ?>"><i class="bi bi-trash"></i> Delete</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No groups match your criteria!</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.usergroups.list");

	$app->renderPage();
?>
