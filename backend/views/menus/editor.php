<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menus/editor.php - Backend menu editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_MENUS))
		$app->redirectWithMessages("/backend/menus/list", array("type" => "error", "content" => "You do not have permission to modify menus"));

	function checkQuery() {
		global $app;

		return	isset($app->request->query["menu_title"]) &&
				isset($app->request->query["menu_id"]);
	}

	$app->vars["menu_title"]	= "";
	$app->vars["menu_id"]		= "";

	if (isset($app->request->query["edit_id"])) {
		if (!$app("menus")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => "Menu does not exist"));

		$menu = $app("menus")->get($app->request->query["edit_id"]);

		if (checkQuery()) {
			$id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["menu_id"]) {
				if ($app("menus")->exists($app->request->query["menu_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => "The ID '{$app->request->query["menu_id"]}' is taken! Using '$id'.");
				} else {
					if ($app->request->query["menu_id"] == "")
						$id = $app->nameToId($app->request->query["menu_title"]);
					else
						$id = $app->nameToId($app->request->query["menu_id"]);

					$app("menus")->delete($app->request->query["edit_id"]);
				}
			}


			$menu->title	= $app->request->query["menu_title"];
			$menu->id		= $id;
			$menu->modified	= time();

			$app("menus")->set($id, $menu);

			if ($app->request->query["menu_id"] == "")
				$app->redirectWithMessages("/backend/menus/editor?edit_id=$id", array("type" => "success", "content" => "Changes saved."));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit Menu";

		$app->vars["menu_title"]	= htmlentities($menu->title);
		$app->vars["menu_id"]		= $menu->id;
	} else {
		$app->vars["title"] = "New Menu";

		if (checkQuery()) {
			if ($app->request->query["menu_id"] == "")
				$id = $app->nameToId($app->request->query["menu_title"]);
			else
				$id = $app->nameToId($app->request->query["menu_id"]);

			while ($app("menus")->exists($id)) $id .= "_1";

			$id = $app->nameToId($id);

			$menu = new Menu(array(
				"id"	=> $id,
				"title"	=> $app->request->query["menu_title"],
				"created" => time(),
				"modified" => time(),
				"items"	=> array()
			));

			$app("menus")->set($id, $menu);

			$app->redirectWithMessages("/backend/menus/editor?edit_id=$id", array("type" => "success", "content" => "Changes saved."));
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
						<label for="menu_title">Menu Title:</label>
						<input type="text" class="form-control" name="menu_title" value="<?php echo $app->vars["menu_title"]; ?>" required />
					</div>

					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="menu_id">Menu ID:</label>
						<input type="text" class="form-control" name="menu_id" placeholder="auto-generate" value="<?php echo $app->vars["menu_id"]; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/menus/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menus.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
