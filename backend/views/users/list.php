<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/list.php - Backend user list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$app->page->setTitle("Users");

	$app->vars["group"] = $app->request->query["group"] ?? null;
	$app->vars["show"] = (is_numeric($app->request->query["show"])) ?  $app->request->query["show"] : 15;
	$app->vars["page"] = (is_numeric($app->request->query["page"])) ? $app->request->query["page"] : 1;

	$users = $app("users")->getAllArr(($app->vars["group"]) ? array("group" => $app->vars["group"]) : null, "modified");

	$app->vars["npages"] = Paginator::numPages($users, $app->vars["show"]);
	$app->vars["users"] = Paginator::Paginate($users, $app->vars["show"], $app->vars["page"]);

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-4 col-xxl-2">
					<h1>Users</h1>
					<span>Show only:</span>
					<form class="d-flex">
						<?php RenderHelper::renderUserGroupPicker("group", null, array("title" => "All Groups", "value" => "null")); ?>
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
						<a class="btn btn-success mt-4 mb-2 d-block ms-auto" href="<?php echo Config::WEBROOT; ?>/backend/users/editor" style="width: 120px;">New User</a>
						<?php
							$baseurl = Config::WEBROOT . "/backend/users/list?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["users"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Id</th>
									<th>Name</th>
									<th>Group</th>
									<th>Created</th>
									<th>Modified</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["users"] as $user) { ?>
									<tr>
										<td><code><?php echo $user->id; ?></code></td>
										<td><?php echo htmlentities($user->name); ?></td>
										<td><?php echo @htmlentities($app("usergroups")->get($user->group)->name); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $user->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $user->modified); ?></td>
										<td>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/users/editor?edit_id=<?php echo $user->id; ?>"><i class="bi bi-pencil"></i> Edit</a>
											<a class="btn btn-primary btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/users/reset_password?reset_id=<?php echo $user->id; ?>"><i class="bi bi-input-cursor-text"></i> Reset Password</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/users/list_bans?user_id=<?php echo $user->id; ?>"><i class="bi bi-dash-circle"></i> Bans</a>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/users/delete?delete_id=<?php echo $user->id; ?>"><i class="bi bi-trash"></i> Delete</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No users match your criteria!</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.list");

	$app->renderPage();
?>
