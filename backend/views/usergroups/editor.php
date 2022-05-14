<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/usergroups/editor.php - Backend user group editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$currentUser = Session::getCurrentSession()->user;
	$formFilled = FormHelper::formFieldsFilled(
		"group_name", "group_id", "group_permissions", "group_rank",
		"group_parent"
	);

	$app->vars["group"] = new UserGroup(array());

	if (!User::userHasPermissions($currentUser, UserPermissions::MODIFY_USERGROUPS))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_usergroups")));

	if (isset($app->request->query["edit_id"])) {
		if (!$app("usergroups")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("usergroup_does_not_exist")));

		$app->vars["group"] = $app("usergroups")->get($app->request->query["edit_id"]);

		if (User::compareUserRank($currentUser, $app->vars["group"]->rank) !== 1)
			$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => $app("i18n")->getString("rank_must_be_less")));

		if ($formFilled) {
			$app->vars["group"]->id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["group_id"]) {
				if ($app("usergroups")->exists($app->request->query["group_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => $app("i18n")->getString("id_taken_using", null, $app->request->query["group_id"], $app->vars["group"]->id));
				} else {
					if ($app->request->query["group_id"] == "")
						$app->vars["group"]->id = $app->nameToId($app->request->query["group_name"]);
					else
						$app->vars["group"]->id = $app->nameToId($app->request->query["group_id"]);

					$app("usergroups")->delete($app->request->query["edit_id"]);
				}
			}

			$parent = $app->request->query["group_parent"];
			if ($parent == $app->vars["group"]->id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_cannot_be_self"));
			}

			$rank = $app->request->query["group_rank"];
			if (User::compareUserRank($currentUser, $rank) !== 1) {
				$rank = 0;
				$app->page->alerts["group_lower"] = array("class" => "warning", "content" => $app("i18n")->getString("rank_must_be_less"));
			}

			$app->vars["group"]->name	= $app->request->query["group_name"];
			$app->vars["group"]->id		= $app->vars["group"]->id;
			$app->vars["group"]->permissions= $app->request->query["group_permissions"];
			$app->vars["group"]->rank	= $rank;
			$app->vars["group"]->modified= time();
			$app->vars["group"]->parent	= $parent;

			$app("usergroups")->set($app->vars["group"]->id, $app->vars["group"]);

			if (UserGroup::userGroupParentLoop($app->vars["group"]->id)) {
				$app->vars["group"]->parent = null;
				$app("usergroups")->set($app->vars["group"]->id, $app->vars["group"]);
				$app->page->alerts["parent_loop"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_loop_avoided"));
			}

			if ($app->request->query["group_id"] == "")
				$app->redirectWithMessages("/backend/usergroups/editor?edit_id=" . $app->vars["group"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}

		$app->vars["title"] = $app("i18n")->getString("edit_usergroup");
	} else {
		$app->vars["title"] = $app("i18n")->getString("new_usergroup");

		if ($formFilled) {
			if ($app->request->query["group_id"] == "")
				$app->vars["group"]->id = $app->nameToId($app->request->query["group_name"]);
			else
				$app->vars["group"]->id = $app->nameToId($app->request->query["group_id"]);

			while ($app("usergroups")->exists($app->vars["group"]->id)) $app->vars["group"]->id .= "_1";

			$parent = $app->request->query["group_parent"];
			if ($parent == $app->vars["group"]->id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_cannot_be_self"));
			}

			$app->vars["group"]->id = $app->nameToId($app->vars["group"]->id);

			$rank = $app->request->query["group_rank"];
			if (User::compareUserRank($currentUser, $rank) !== 1) {
				$rank = 0;
				$app->page->alerts["group_lower"] = array("class" => "warning", "content" => $app("i18n")->getString("rank_must_be_less"));
			}

			$app->vars["group"]->name =		$app->request->query["group_name"];
			$app->vars["group"]->permissions=$app->request->query["group_permissions"];
			$app->vars["group"]->rank =		$rank;
			$app->vars["group"]->created =	time();
			$app->vars["group"]->modified =	time();
			$app->vars["group"]->parent =	$parent;

			$app("usergroups")->set($app->vars["group"]->id, $app->vars["group"]);

			$app->redirectWithMessages("/backend/usergroups/editor?edit_id=" . $app->vars["group"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));
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
						<label for="group_name"><?php $app("i18n")("group_name_c"); ?></label>
						<input type="text" class="form-control" name="group_name" value="<?php echo $app->vars["group"]->name; ?>" required />

						<label for="group_permissions"><?php $app("i18n")("group_permissions_c"); ?></label>
						<input type="number" class="form-control" id="group_permissions" name="group_permissions" value="<?php echo $app->vars["group"]->permissions; ?>" required />

						<div class="permissions-picker">
							<hr />
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_1" name="perm_1" />
								<label class="form-check-label" for="perm_1"><?php $app("i18n")("log_in"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_2" name="perm_2" />
								<label class="form-check-label" for="perm_2"><?php $app("i18n")("log_in_backend"); ?></label>
							</div>
							<hr />
							<!--<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_4096" name="perm_4096" />
								<label class="form-check-label" for="perm_4096"><?php $app("i18n")("view_comments"); ?></label>
							</div>-->
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_8192" name="perm_8192" />
								<label class="form-check-label" for="perm_8192"><?php $app("i18n")("post_comments"); ?></label>
							</div>
							<!--<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_16384" name="perm_16384" />
								<label class="form-check-label" for="perm_16384"><?php $app("i18n")("modify_comments_self"); ?></label>
							</div>-->
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_32768" name="perm_32768" />
								<label class="form-check-label" for="perm_32768"><?php $app("i18n")("modify_comments"); ?></label>
							</div>
							<hr />
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_256" name="perm_256" />
								<label class="form-check-label" for="perm_256"><?php $app("i18n")("modify_articles_self"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_512" name="perm_512" />
								<label class="form-check-label" for="perm_512"><?php $app("i18n")("modify_articles"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_1024" name="perm_1024" />
								<label class="form-check-label" for="perm_1024"><?php $app("i18n")("modify_categories"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_2048" name="perm_2048" />
								<label class="form-check-label" for="perm_2048"><?php $app("i18n")("view_unpublished_content"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_65536" name="perm_65536" />
								<label class="form-check-label" for="perm_65536"><?php $app("i18n")("modify_media"); ?></label>
							</div>
							<hr />
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_4" name="perm_4" />
								<label class="form-check-label" for="perm_4"><?php $app("i18n")("modify_users_self"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_8" name="perm_8" />
								<label class="form-check-label" for="perm_8"><?php $app("i18n")("modify_users"); ?></label>
							</div>
							<!--<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_16" name="perm_16" />
								<label class="form-check-label" for="perm_16"><?php $app("i18n")("approve_users"); ?></label>
							</div>-->
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_32" name="perm_32" />
								<label class="form-check-label" for="perm_32"><?php $app("i18n")("ban_users"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_64" name="perm_64" />
								<label class="form-check-label" for="perm_64"><?php $app("i18n")("modify_usergroups"); ?></label>
							</div>
							<hr />
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_1048576" name="perm_1048576" />
								<label class="form-check-label" for="perm_1048576"><?php $app("i18n")("modify_menus"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_2097152" name="perm_2097152" />
								<label class="form-check-label" for="perm_2097152"><?php $app("i18n")("modify_modules"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_16777216" name="perm_16777216" />
								<label class="form-check-label" for="perm_16777216"><?php $app("i18n")("modify_plugins"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_4194304" name="perm_4194304" />
								<label class="form-check-label" for="perm_4194304"><?php $app("i18n")("modify_settings"); ?></label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_8388608" name="perm_8388608" />
								<label class="form-check-label" for="perm_8388608"><?php $app("i18n")("use_installer"); ?></label>
							</div>
						</div>

						<script>
							function syncBox() {
								var val = 0;
								if (document.getElementById("perm_1").checked) val |= 1;
								if (document.getElementById("perm_2").checked) val |= 2;
								if (document.getElementById("perm_4").checked) val |= 4;
								if (document.getElementById("perm_8").checked) val |= 8;
								//if (document.getElementById("perm_16").checked) val |= 16;
								if (document.getElementById("perm_32").checked) val |= 32;
								if (document.getElementById("perm_64").checked) val |= 64;
								if (document.getElementById("perm_256").checked) val |= 256;
								if (document.getElementById("perm_512").checked) val |= 512;
								if (document.getElementById("perm_1024").checked) val |= 1024;
								if (document.getElementById("perm_2048").checked) val |= 2048;
								//if (document.getElementById("perm_4096").checked) val |= 4096;
								if (document.getElementById("perm_8192").checked) val |= 8192;
								//if (document.getElementById("perm_16384").checked) val |= 16384;
								if (document.getElementById("perm_32768").checked) val |= 32768;
								if (document.getElementById("perm_65536").checked) val |= 65536;
								if (document.getElementById("perm_1048576").checked) val |= 1048576;
								if (document.getElementById("perm_2097152").checked) val |= 2097152;
								if (document.getElementById("perm_4194304").checked) val |= 4194304;
								if (document.getElementById("perm_8388608").checked) val |= 8388608;
								if (document.getElementById("perm_16777216").checked) val |= 16777216;
								document.getElementById("group_permissions").value = val;
							}

							function syncChecks() {
								var val = document.getElementById("group_permissions").value;
								document.getElementById("perm_1").checked = val & 1;
								document.getElementById("perm_2").checked = val & 2;
								document.getElementById("perm_4").checked = val & 4;
								document.getElementById("perm_8").checked = val & 8;
								//document.getElementById("perm_16").checked = val & 16;
								document.getElementById("perm_32").checked = val & 32;
								document.getElementById("perm_64").checked = val & 64;
								document.getElementById("perm_256").checked = val & 256;
								document.getElementById("perm_512").checked = val & 512;
								document.getElementById("perm_1024").checked = val & 1024;
								document.getElementById("perm_2048").checked = val & 2048;
								//document.getElementById("perm_4096").checked = val & 4096;
								document.getElementById("perm_8192").checked = val & 8192;
								//document.getElementById("perm_16384").checked = val & 16384;
								document.getElementById("perm_32768").checked = val & 32768;
								document.getElementById("perm_65536").checked = val & 65536;
								document.getElementById("perm_1048576").checked = val & 1048576;
								document.getElementById("perm_2097152").checked = val & 2097152;
								document.getElementById("perm_4194304").checked = val & 4194304;
								document.getElementById("perm_8388608").checked = val & 8388608;
								document.getElementById("perm_16777216").checked = val & 16777216;
							}

							setTimeout(syncChecks, 200);
							document.getElementById("group_permissions").onchange = syncChecks;
							document.getElementById("perm_1").onchange = syncBox;
							document.getElementById("perm_2").onchange = syncBox;
							document.getElementById("perm_4").onchange = syncBox;
							document.getElementById("perm_8").onchange = syncBox;
							//document.getElementById("perm_16").onchange = syncBox;
							document.getElementById("perm_32").onchange = syncBox;
							document.getElementById("perm_64").onchange = syncBox;
							document.getElementById("perm_256").onchange = syncBox;
							document.getElementById("perm_512").onchange = syncBox;
							document.getElementById("perm_1024").onchange = syncBox;
							document.getElementById("perm_2048").onchange = syncBox;
							//document.getElementById("perm_4096").onchange = syncBox;
							document.getElementById("perm_8192").onchange = syncBox;
							//document.getElementById("perm_16384").onchange = syncBox;
							document.getElementById("perm_32768").onchange = syncBox;
							document.getElementById("perm_65536").onchange = syncBox;
							document.getElementById("perm_1048576").onchange = syncBox;
							document.getElementById("perm_2097152").onchange = syncBox;
							document.getElementById("perm_4194304").onchange = syncBox;
							document.getElementById("perm_8388608").onchange = syncBox;
							document.getElementById("perm_16777216").onchange = syncBox;
						</script>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="group_id"><?php $app("i18n")("group_id_c"); ?></label>
						<input type="text" class="form-control" name="group_id" placeholder="auto-generate" value="<?php echo $app->vars["group"]->id; ?>" />

						<label for="group_parent"><?php $app("i18n")("group_parent_c"); ?></label>
						<?php RenderHelper::renderUserGroupPicker("group_parent", $app->vars["group"]->parent, array("title" => "[none]", "value" => "")); ?>

						<label for="group_rank"><?php $app("i18n")("group_rank_c"); ?></label>
						<input type="number" class="form-control" name="group_rank" value="<?php echo $app->vars["group"]->rank; ?>" min="-1" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/list" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("save"); ?></button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.usergroups.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
