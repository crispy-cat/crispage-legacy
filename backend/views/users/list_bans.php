<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/list_bans.php - Backend user ban list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["user_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "No ID specified"));

	$user = $app("users")->get($app->request->query["user_id"]);
	if (!$user)
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => "User does not exist"));

	$app->page->setTitle("Bans");

	$app->vars["show"] = (is_numeric($app->request->query["show"])) ?  $app->request->query["show"] : 15;
	$app->vars["page"] = (is_numeric($app->request->query["page"])) ? $app->request->query["page"] : 1;

	$bans = $app("bans")->getAllArr(array("user" => $app->request->query["user_id"]), "modified");

	$app->vars["npages"] = Paginator::numPages($bans, $app->vars["show"]);
	$app->vars["bans"] = Paginator::Paginate($bans, $app->vars["show"], $app->vars["page"]);

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-2">
					<h1>User Bans</h1>
					<p>'<?php echo $app->request->query["user_id"]; ?>'</p>
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
				<div class="col-12 col-md-10">
					<div style="float: right;">
						<div class="btn-group mt-4 mb-2 d-block ms-auto">
							<a class="btn btn-warning" href="<?php echo Config::WEBROOT; ?>/backend/users/ban_user?user_id=<?php echo $app->request->query["user_id"]; ?>" style="width: 90px;">New Ban</a>
							<a class="btn btn-warning" href="<?php echo Config::WEBROOT; ?>/backend/users/unban_user?user_id=<?php echo $app->request->query["user_id"]; ?>" style="width: 110px;">Unban User</a>
						</div>
						<?php
							$baseurl = "/backend/users/list_bans?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php if (count($app->vars["bans"])) { ?>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Expires</th>
									<th>Reason</th>
									<th>Created</th>
									<th>Modified</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($app->vars["bans"] as $ban) { ?>
									<tr>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $ban->expires); ?></td>
										<td><?php echo htmlentities($ban->reason); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $ban->created); ?></td>
										<td><?php echo date($app->getSetting("date_format", "Y-m-d"), $ban->modified); ?></td>
										<td>
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/users/delete_ban?delete_id=<?php echo $ban->id; ?>"><i class="bi bi-trash"></i> Delete</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p>No bans match your criteria!</p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.list");

	$app->renderPage();
?>
