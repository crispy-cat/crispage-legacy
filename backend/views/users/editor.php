<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/users/editor.php - Backend user editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	function checkQuery() {
		global $app;

		return	isset($app->request->query["user_name"]) &&
				isset($app->request->query["user_id"]) &&
				isset($app->request->query["user_email"]) &&
				isset($app->request->query["user_group"]) &&
				filter_var($app->request->query["user_email"], FILTER_VALIDATE_EMAIL);
	}

	$app->vars["user_name"]		= "";
	$app->vars["user_id"]		= "";
	$app->vars["user_email"]	= "";
	$app->vars["user_group"]	= null;

	if (isset($app->request->query["edit_id"])) {
		if (!$app->users->existsUser($app->request->query["edit_id"]))
			$app->redirect(Config::WEBROOT . "/backend/menu_users/list?me=Menu user does not exist");

		$user = $app->users->getUser($app->request->query["edit_id"]);

		if ($user->id == $app->session->getCurrentSession()->user) {
			if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_SELF))
				$app->redirect(Config::WEBROOT . "/backend/users/list?me=You do not have permission to modify yourself");
		} else {
			if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_USERS))
				$app->redirect(Config::WEBROOT . "/backend/users/list?me=You do not have permission to modify other users");
		}

		if (checkQuery()) {
			$id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["user_id"]) {
				if ($app->users->existsUser($app->request->query["user_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => "The ID '{$app->request->query["user_id"]}' is taken! Using '$id'.");
				} else {
					if ($app->request->query["user_id"] == "")
						$id = $app->nameToId($app->request->query["user_name"]);
					else
						$id = $app->nameToId($app->request->query["user_id"]);

					$app->users->deleteUser($app->request->query["edit_id"]);
				}
			}

			$user->name		= $app->request->query["user_name"];
			$user->email	= $app->request->query["user_email"];
			$user->id		= $id;
			$user->group	= $app->request->query["user_group"];
			$user->modified	= time();

			$app->menus->setUser($id, $user);

			if ($app->request->query["user_id"] == "")
				$app->redirect(Config::WEBROOT . "/backend/users/editor?edit_id=$id&ms=Changes saved.");

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit User";

		$app->vars["user_name"]	= htmlentities($user->name);
		$app->vars["user_id"]	= $user->id;
		$app->vars["user_email"]= htmlentities($user->email);
		$app->vars["user_group"]= htmlentities($user->group);
	} else {
		$app->vars["title"] = "New User";

		if (checkQuery()) {
			if (!$app->users->userHasPermissions($app->session->getCurrentSession()->user, UserPermissions::MODIFY_USERS))
				$app->redirect(Config::WEBROOT . "/backend/users/list?me=You do not have permission to create users");

			if ($app->request->query["user_id"] == "")
				$id = $app->nameToId($app->request->query["user_name"]);
			else
				$id = $app->nameToId($app->request->query["user_id"]);

			while ($app->users->existsUser($id)) $id .= "_1";

			$id = $app->nameToId($id);

			$user = new User(array(
				"id"		=> $id,
				"name"		=> $app->request->query["user_name"],
				"email"		=> $app->request->query["user_email"],
				"group"		=> $app->request->query["user_group"],
				"created"	=> time(),
				"modified"	=> time(),
				"loggedin"	=> 0,
				"activated"	=> 2
			));

			$app->users->setUser($id, $user);

			$app->redirect(Config::WEBROOT . "/backend/users/editor?edit_id=$id&ms=Changes saved.");
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
			<div class="row">
				<form method="post" class="d-flex">
					<div class="col col-lg-8 me-lg-2">
						<?php if (isset($app->request->query["edit_id"])) { ?>
							<input type="hidden" name="edit_id" value="<?php echo $app->request->query["edit_id"]; ?>" />
						<?php } ?>
						<label for="user_name">User Name:</label>
						<input type="text" class="form-control" name="user_name" value="<?php echo $app->vars["user_name"]; ?>" required />

						<label for="user_email">User Email:</label>
						<input type="email" class="form-control" name="user_email" value="<?php echo $app->vars["user_email"]; ?>" required />
					</div>
					<div class="col col-lg-4 ms-lg-2">
						<label for="user_id">User ID:</label>
						<input type="text" class="form-control" name="user_id" placeholder="auto-generate" value="<?php echo $app->vars["user_id"]; ?>" />

						<label for="user_group">User Group:</label>
						<?php RenderHelper::renderUserGroupPicker("user_group", $app->vars["user_group"]); ?>

						<a class="btn btn-secondary btn-lg mt-3 me-2" href="<?php echo Config::WEBROOT; ?>/backend/users/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</form>
			</div>
		</div>
<?php
	});

	$app->events->trigger("backend.view.users.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
