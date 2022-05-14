<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menus/editor.php - Backend menu editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	$currentUser = Session::getCurrentSession()->user;
	$formFilled = FormHelper::formFieldsFilled(
		"menu_title", "menu_id"
	);

	$app->vars["menu"] = new Menu(array());

	if (!User::userHasPermissions($currentUser, UserPermissions::MODIFY_MENUS))
		$app->redirectWithMessages("/backend/menus/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_menus")));

	if (isset($app->request->query["edit_id"])) {
		if (!$app("menus")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/articles/list", array("type" => "error", "content" => $app("i18n")->getString("menu_does_not_exist")));

		$app->vars["menu"] = $app("menus")->get($app->request->query["edit_id"]);

		if ($formFilled) {
			$app->vars["menu"]->id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["menu_id"]) {
				if ($app("menus")->exists($app->request->query["menu_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => $app("i18n")->getString("id_taken_using", null, $app->request->query["menu_id"], $app->vars["menu"]->id));
				} else {
					if ($app->request->query["menu_id"] == "")
						$app->vars["menu"]->id = $app->nameToId($app->request->query["menu_title"]);
					else
						$app->vars["menu"]->id = $app->nameToId($app->request->query["menu_id"]);

					$app("menus")->delete($app->request->query["edit_id"]);
				}
			}


			$app->vars["menu"]->title	= $app->request->query["menu_title"];
			$app->vars["menu"]->id		= $app->vars["menu"]->id;
			$app->vars["menu"]->modified	= time();

			$app("menus")->set($app->vars["menu"]->id, $app->vars["menu"]);

			if ($app->request->query["menu_id"] == "")
				$app->redirectWithMessages("/backend/menus/editor?edit_id=" . $app->vars["menu"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}

		$app->vars["title"] = $app("i18n")->getString("edit_menu");
	} else {
		$app->vars["title"] = $app("i18n")->getString("new_menu");

		if ($formFilled) {
			if ($app->request->query["menu_id"] == "")
				$app->vars["menu"]->id = $app->nameToId($app->request->query["menu_title"]);
			else
				$app->vars["menu"]->id = $app->nameToId($app->request->query["menu_id"]);

			while ($app("menus")->exists($app->vars["menu"]->id)) $app->vars["menu"]->id .= "_1";

			$app->vars["menu"]->id = $app->nameToId($app->vars["menu"]->id);

			$app->vars["menu"]->title =		$app->request->query["menu_title"];
			$app->vars["menu"]->create =	time();
			$app->vars["menu"]->modified =	time();

			$app("menus")->set($app->vars["menu"]->id, $app->vars["menu"]);

			$app->redirectWithMessages("/backend/menus/editor?edit_id=" . $app->vars["menu"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));
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
						<label for="menu_title"><?php $app("i18n")("menu_title_c"); ?></label>
						<input type="text" class="form-control" name="menu_title" value="<?php echo $app->vars["menu"]->title; ?>" required />
					</div>

					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="menu_id"><?php $app("i18n")("menu_id_c"); ?></label>
						<input type="text" class="form-control" name="menu_id" placeholder="auto-generate" value="<?php echo $app->vars["menu"]->id; ?>" />

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
