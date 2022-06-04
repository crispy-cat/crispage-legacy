<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/list_bans.php - Backend user ban list page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	if (!isset($app->request->query["user_id"]))
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_id_given")));

	$user = $app("users")->get($app->request->query["user_id"]);
	if (!$user)
		$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("user_does_not_exist")));

	$app->page->setTitle($app("i18n")->getString("bans"));

	\Crispage\Helpers\Paginator::paginationQuery($app->vars);

	$bans = $app("bans")->getAllArr(array("user" => $app->request->query["user_id"]), "expires", true);

	\Crispage\Helpers\Paginator::paginateNum($app->vars, $bans, "bans");

	$app->page->setContent(function($app) {
?>
		<div id="main" class="page-content">
			<div class="row">
				<div class="col-12 col-md-2">
					<h1><?php $app("i18n")("bans"); ?></h1>
					<p>'<?php echo $app->request->query["user_id"]; ?>'</p>
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
				<div class="col-12 col-md-10">
					<div style="float: right;">
						<div class="btn-group mt-4 mb-2 d-block ms-auto">
							<a class="btn btn-warning" href="<?php echo Config::WEBROOT; ?>/backend/users/ban_user?user_id=<?php echo $app->request->query["user_id"]; ?>" style="width: 90px;"><?php $app("i18n")("new_ban"); ?></a>
							<a class="btn btn-warning" href="<?php echo Config::WEBROOT; ?>/backend/users/unban_user?user_id=<?php echo $app->request->query["user_id"]; ?>" style="width: 110px;"><?php $app("i18n")("unban_user"); ?></a>
						</div>
						<?php
							$baseurl = "/backend/users/list_bans?show=" . (($app->vars["show"]) ? $app->vars["show"] : "all") . "&page=";
							\Crispage\Helpers\RenderHelper::renderPagination($baseurl, $app->vars["npages"], $app->vars["page"] ?? 1);
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
									<th><?php $app("i18n")("expires"); ?></th>
									<th><?php $app("i18n")("reason"); ?></th>
									<th><?php $app("i18n")("created"); ?></th>
									<th><?php $app("i18n")("modified"); ?></th>
									<th><?php $app("i18n")("actions"); ?></th>
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
											<a class="btn btn-danger btn-sm" href="<?php echo Config::WEBROOT; ?>/backend/users/delete_ban?delete_id=<?php echo $ban->id; ?>">
												<i class="bi bi-trash"></i> <?php $app("i18n")("delete"); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<p><?php $app("i18n")("no_bans_match"); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.list");

	$app->renderPage();
?>
