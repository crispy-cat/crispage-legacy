<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/usergroups/editor.php - Backend user group editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_USERGROUPS))
		$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => "You do not have permission to modify usergroups"));

	function checkQuery() {
		global $app;

		return	isset($app->request->query["group_name"]) &&
				isset($app->request->query["group_id"]) &&
				isset($app->request->query["group_permissions"]) &&
				isset($app->request->query["group_parent"]);
	}

	$app->vars["group_name"]		= "";
	$app->vars["group_id"]			= "";
	$app->vars["group_permissions"]	= 0;
	$app->vars["group_parent"]		= null;

	if (isset($app->request->query["edit_id"])) {
		if (!$app->users->existsUserGroup($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/usergroups/list", array("type" => "error", "content" => "Group does not exist"));

		$group = $app->users->getUserGroup($app->request->query["edit_id"]);

		if (checkQuery()) {
			$id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["group_id"]) {
				if ($app->users->existsUserGroup($app->request->query["group_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => "The ID '{$app->request->query["group_id"]}' is taken! Using '$id'.");
				} else {
					if ($app->request->query["group_id"] == "")
						$id = $app->nameToId($app->request->query["group_name"]);
					else
						$id = $app->nameToId($app->request->query["group_id"]);

					$app->users->deleteUserGroup($app->request->query["edit_id"]);
				}
			}

			$parent = $app->request->query["group_parent"];
			if ($parent == $id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => "Parent group cannot be self.");
			}

			$group->name	= $app->request->query["group_name"];
			$group->id		= $id;
			$group->permissions= $app->request->query["group_permissions"];
			$group->modified	= time();
			$group->parent	= $parent;

			$app->users->setUserGroup($id, $group);

			if ($app->users->userGroupParentLoop($id)) {
				$group->parent = null;
				$app->users->setUserGroup($id, $group);
				$app->page->alerts["parent_loop"] = array("class" => "warning", "content" => "Parent group cannot cause an infinite loop.");
			}

			if ($app->request->query["group_id"] == "")
				$app->redirectWithMessages("/backend/usergroups/editor?edit_id=$id", array("type" => "success", "content" => "Changes saved."));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit Group";

		$app->vars["group_name"]	= htmlentities($group->name);
		$app->vars["group_id"]		= $group->id;
		$app->vars["group_permissions"] = htmlentities($group->permissions);
		$app->vars["group_parent"]	= htmlentities($group->parent);
	} else {
		$app->vars["title"] = "New Group";

		if (checkQuery()) {
			if ($app->request->query["group_id"] == "")
				$id = $app->nameToId($app->request->query["group_name"]);
			else
				$id = $app->nameToId($app->request->query["group_id"]);

			while ($app->users->existsUserGroup($id)) $id .= "_1";

			$parent = $app->request->query["group_parent"];
			if ($parent == $id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => "Parent group cannot be self.");
			}

			$id = $app->nameToId($id);
			$group = new UserGroup(array(
				"id"		=> $id,
				"name"		=> $app->request->query["group_name"],
				"permissions"=> $app->request->query["group_permissions"],
				"created"	=> time(),
				"modified"	=> time(),
				"parent"	=> $parent
			));

			$app->users->setUserGroup($id, $group);

			$app->redirectWithMessages("/backend/usergroups/editor?edit_id=$id", array("type" => "success", "content" => "Changes saved."));
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
						<label for="group_name">Group Name:</label>
						<input type="text" class="form-control" name="group_name" value="<?php echo $app->vars["group_name"]; ?>" required />

						<label for="group_permissions">Group Permissions:</label>
						<input type="number" class="form-control" id="group_permissions" name="group_permissions" value="<?php echo $app->vars["group_permissions"]; ?>" required />

						<div class="permissions-picker">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_1" name="perm_1" />
								<label class="form-check-label" for="perm_1">Log in</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_2" name="perm_2" />
								<label class="form-check-label" for="perm_2">Log in (Backend)</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_4" name="perm_4" />
								<label class="form-check-label" for="perm_4">Modify Users (Self)</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_8" name="perm_8" />
								<label class="form-check-label" for="perm_8">Modify Users</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_16" name="perm_16" />
								<label class="form-check-label" for="perm_16">Approve Users</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_32" name="perm_32" />
								<label class="form-check-label" for="perm_32">Ban Users</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_64" name="perm_64" />
								<label class="form-check-label" for="perm_64">Modify Usergroups</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_256" name="perm_256" />
								<label class="form-check-label" for="perm_256">Modify Articles (Self)</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_512" name="perm_512" />
								<label class="form-check-label" for="perm_512">Modify Articles</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_1024" name="perm_1024" />
								<label class="form-check-label" for="perm_1024">Modify Categories</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_4096" name="perm_4096" />
								<label class="form-check-label" for="perm_4096">View Comments</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_8192" name="perm_8192" />
								<label class="form-check-label" for="perm_8192">Post Comments</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_16384" name="perm_16384" />
								<label class="form-check-label" for="perm_16384">Modify Comments (Self)</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_32768" name="perm_32768" />
								<label class="form-check-label" for="perm_32768">Modify Comments</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_65536" name="perm_65536" />
								<label class="form-check-label" for="perm_65536">Modify Media</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_1048576" name="perm_1048576" />
								<label class="form-check-label" for="perm_1048576">Modify Menus</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_2097152" name="perm_2097152" />
								<label class="form-check-label" for="perm_2097152">Modify Modules</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_4194304" name="perm_4194304" />
								<label class="form-check-label" for="perm_4194304">Modify Settings</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_8388608" name="perm_8388608" />
								<label class="form-check-label" for="perm_8388608">Use Installer</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="perm_16777216" name="perm_16777216" />
								<label class="form-check-label" for="perm_16777216">Modify Plugins</label>
							</div>
						</div>

						<script>
							function syncBox() {
								var val = 0;
								if (document.getElementById("perm_1").checked) val |= 1;
								if (document.getElementById("perm_2").checked) val |= 2;
								if (document.getElementById("perm_4").checked) val |= 4;
								if (document.getElementById("perm_8").checked) val |= 8;
								if (document.getElementById("perm_16").checked) val |= 16;
								if (document.getElementById("perm_32").checked) val |= 32;
								if (document.getElementById("perm_64").checked) val |= 64;
								if (document.getElementById("perm_256").checked) val |= 256;
								if (document.getElementById("perm_512").checked) val |= 512;
								if (document.getElementById("perm_1024").checked) val |= 1024;
								if (document.getElementById("perm_4096").checked) val |= 4096;
								if (document.getElementById("perm_8192").checked) val |= 8192;
								if (document.getElementById("perm_16384").checked) val |= 16384;
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
								document.getElementById("perm_16").checked = val & 16;
								document.getElementById("perm_32").checked = val & 32;
								document.getElementById("perm_64").checked = val & 64;
								document.getElementById("perm_256").checked = val & 256;
								document.getElementById("perm_512").checked = val & 512;
								document.getElementById("perm_1024").checked = val & 1024;
								document.getElementById("perm_4096").checked = val & 4096;
								document.getElementById("perm_8192").checked = val & 8192;
								document.getElementById("perm_16384").checked = val & 16384;
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
							document.getElementById("perm_16").onchange = syncBox;
							document.getElementById("perm_32").onchange = syncBox;
							document.getElementById("perm_64").onchange = syncBox;
							document.getElementById("perm_256").onchange = syncBox;
							document.getElementById("perm_512").onchange = syncBox;
							document.getElementById("perm_1024").onchange = syncBox;
							document.getElementById("perm_4096").onchange = syncBox;
							document.getElementById("perm_8192").onchange = syncBox;
							document.getElementById("perm_16384").onchange = syncBox;
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
						<label for="group_id">Group ID:</label>
						<input type="text" class="form-control" name="group_id" placeholder="auto-generate" value="<?php echo $app->vars["group_id"]; ?>" />

						<label for="group_parent">Group Parent:</label>
						<?php RenderHelper::renderUserGroupPicker("group_parent", $app->vars["group_parent"], array("title" => "[none]", "value" => "")); ?>

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.usergroups.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
