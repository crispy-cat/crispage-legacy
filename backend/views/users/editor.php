<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/editor.php - Backend user editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$currentUser = \Crispage\Assets\Session::getCurrentSession()->user;
	$formFilled = \Crispage\Helpers\FormHelper::formFieldsFilled(
		"user_name", "user_id", "user_email", "user_group"
	) && filter_var($app->request->query["user_email"], FILTER_VALIDATE_EMAIL);

	$app->vars["user"] = new \Crispage\Assets\User(array());

	if (isset($app->request->query["edit_id"])) {
		if (!$app("users")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("user_does_not_exist")));

		$app->vars["user"] = $app("users")->get($app->request->query["edit_id"]);

		if ($app->vars["user"]->id == $currentUser) {
			if (!\Crispage\Assets\User::userHasPermissions($currentUser, \Crispage\Users\UserPermissions::MODIFY_SELF))
				$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_self")));
		} else {
			if (!\Crispage\Assets\User::userHasPermissions($currentUser, \Crispage\Users\UserPermissions::MODIFY_USERS))
				$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_users")));

				if (\Crispage\Assets\User::compareUserRank($currentUser, $app->vars["user"]->id) !== 1)
					$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("rank_must_be_less")));
		}

		if ($form_filled) {
			$app->vars["user"]->id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["user_id"]) {
				if ($app("users")->exists($app->request->query["user_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => $app("i18n")->getString("id_taken_using", null, $app->request->query["user_id"], $app->vars["user"]->id));
				} else {
					if ($app->request->query["user_id"] == "")
						$app->vars["user"]->id = $app->nameToId($app->request->query["user_name"]);
					else
						$app->vars["user"]->id = $app->nameToId($app->request->query["user_id"]);

					$app("users")->delete($app->request->query["edit_id"]);
				}
			}

			$group = $app->request->query["user_group"];
			if (\Crispage\Assets\User::compareUserRank($currentUser, \Crispage\Assets\UserGroup::getGroupRank($group)) !== 1) {
				$group = $app->getSetting("users.default_group");
				$app->page->alerts["group_lower"] = array("class" => "warning", "content" => $app("i18n")->getString("rank_must_be_less"));
			}

			$app->vars["user"]->name	= $app->request->query["user_name"];
			$app->vars["user"]->email	= $app->request->query["user_email"];
			$app->vars["user"]->group	= $group;
			$app->vars["user"]->modified= time();

			$app("users")->set($app->vars["user"]->id, $app->vars["user"]);

			if ($app->request->query["user_id"] == "")
				$app->redirectWithMessages("/backend/users/editor?edit_id=" . $app->vars["user"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}

		$app->vars["title"] = $app("i18n")->getString("edit_user");
	} else {
		$app->vars["title"] = $app("i18n")->getString("new_user");

		if ($formFilled) {
			if (!\Crispage\Assets\User::userHasPermissions($currentUser, \Crispage\Users\UserPermissions::MODIFY_USERS))
				$app->redirectWithMessages("/backend/users/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_users")));

			if ($app->request->query["user_id"] == "")
				$app->vars["user"]->id = $app->nameToId($app->request->query["user_name"]);
			else
				$app->vars["user"]->id = $app->nameToId($app->request->query["user_id"]);

			while ($app("users")->exists($app->vars["user"]->id)) $app->vars["user"]->id .= "_1";

			$app->vars["user"]->id = $app->nameToId($app->vars["user"]->id);

			$group = $app->request->query["user_group"];
			if (\Crispage\Assets\User::compareUserRank($currentUser, \Crispage\Assets\UserGroup::getGroupRank($group)) !== 1) {
				$group = $app->getSetting("users.default_group");
				$app->page->alerts["group_lower"] = array("class" => "warning", "content" => $app("i18n")->getString("rank_must_be_less"));
			}

			$app->vars["user"]->name	= $app->request->query["user_name"];
			$app->vars["user"]->email	= $app->request->query["user_email"];
			$app->vars["user"]->group	= $group;
			$app->vars["user"]->created = time();
			$app->vars["user"]->modified= time();
			$app->vars["user"]->loggedin= 0;
			$app->vars["user"]->activated=2;

			$app("users")->set($app->vars["user"]->id, $app->vars["user"]);

			$app->redirectWithMessages("/backend/users/editor?edit_id=" . $app->vars["user"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));
		}
	}

	$app->page->setTitle($app->vars["title"]);

	$app->page->setContent(function($app) {
?>

		<div id="main" class="page-content">
			<div class="row">
				<div class="col">
					<h1><?php echo $app->vars["title"]; ?></h1>
				</div>
			</div>
			<form method="post">
				<div class="row">
					<div class="col-12 col-lg-8 pe-lg-2">
						<?php if (isset($app->request->query["edit_id"])) { ?>
							<input type="hidden" name="edit_id" value="<?php echo $app->request->query["edit_id"]; ?>" />
						<?php } ?>
						<label for="user_name"><?php $app("i18n")("user_name_c"); ?></label>
						<input type="text" class="form-control" name="user_name" value="<?php echo $app->vars["user"]->name; ?>" required />

						<label for="user_email"><?php $app("i18n")("user_email_c"); ?></label>
						<input type="email" class="form-control" name="user_email" value="<?php echo $app->vars["user"]->email; ?>" required />
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="user_id"><?php $app("i18n")("user_id_c"); ?></label>
						<input type="text" class="form-control" name="user_id" placeholder="auto-generate" value="<?php echo $app->vars["user"]->id; ?>" />

						<label for="user_group"><?php $app("i18n")("user_group_c"); ?></label>
						<?php \Crispage\Helpers\RenderHelper::renderUserGroupPicker("user_group", $app->vars["user"]->group); ?>

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/users/list" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("save"); ?></button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
