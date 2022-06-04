<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menu_items/editor.php - Backend menu item editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/backend/header.php";

	$currentUser = \Crispage\Assets\Session::getCurrentSession()->user;
	$formFilled = \Crispage\Helpers\FormHelper::formFieldsFilled(
		"item_label", "item_id", "item_type", "item_menu",
		"item_parent", "item_ord"
	);

	$app->vars["item"] = new \Crispage\Assets\MenuItem(array());

	if (!\Crispage\Assets\User::userHasPermissions($currentUser, \Crispage\Users\UserPermissions::MODIFY_MENUS))
		$app->redirectWithMessages("/backend/menu_items/list", array("type" => "error", "content" => $app("i18n")->getString("no_permission_menus")));

	if (isset($app->request->query["edit_id"])) {
		if (!$app("menu_items")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/menu_items/list", array("type" => "error", "content" => $app("i18n")->getString("menu_item_does_not_exist")));

		$app->vars["item"] = $app("menu_items")->get($app->request->query["edit_id"]);

		if ($formFilled) {
			$app->vars["item"]->id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["item_id"]) {
				if ($app("menu_items")->exists($app->request->query["item_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => $app("i18n")->getString("id_taken_using", null, $app->request->query["item_id"], $app->vars["item"]->id));
				} else {
					if ($app->request->query["item_id"] == "")
						$app->vars["item"]->id = $app->nameToId($app->request->query["item_label"]);
					else
						$app->vars["item"]->id = $app->nameToId($app->request->query["item_id"]);

					$app("menu_items")->delete($app->request->query["edit_id"]);
				}
			}

			$parent = $app->request->query["item_parent"];
			if ($parent == $app->vars["item"]->id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_cannot_be_self"));
			}

			$app->vars["item"]->label	= $app->request->query["item_label"];
			$app->vars["item"]->content	= $app->request->query["item_content"] ?? "";
			$app->vars["item"]->type	= $app->request->query["item_type"];
			$app->vars["item"]->id		= $app->vars["item"]->id;
			$app->vars["item"]->menu	= $app->request->query["item_menu"];
			$app->vars["item"]->parent	= $parent;
			$app->vars["item"]->ord		= $app->request->query["item_ord"];
			$app->vars["item"]->modified= time();

			$app("menu_items")->set($app->vars["item"]->id, $app->vars["item"]);

			if (\Crispage\Assets\Asset::parentLoop("menu_items", $app->vars["item"]->id)) {
				$app->vars["item"]->parent = null;
				$app("menu_items")->set($app->vars["item"]->id, $app->vars["item"]);
				$app->page->alerts["parent_loop"] = array("class" => "warning", "content" => $app("i18n")->getString("parent_loop_avoided"));
			}

			if ($app->request->query["item_id"] == "")
				$app->redirectWithMessages("/backend/menu_items/editor?edit_id=" . $app->vars["item"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => $app("i18n")->getString("changes_saved"));
		}

		$app->vars["title"] = $app("i18n")->getString("edit_menu_item");
	} else {
		$app->vars["title"] = $app("i18n")->getString("new_menu_item");

		if ($formFilled) {
			if ($app->request->query["item_id"] == "")
				$app->vars["item"]->id = $app->nameToId($app->request->query["item_label"]);
			else
				$app->vars["item"]->id = $app->nameToId($app->request->query["item_id"]);

			while ($app("menu_items")->exists($app->vars["item"]->id)) $app->vars["item"]->id .= "_1";

			$app->vars["item"]->id = $app->nameToId($app->vars["item"]->id);

			$app->vars["item"]->label =		$app->request->query["item_label"];
			$app->vars["item"]->content =	$app->request->query["item_content"] ?? "";
			$app->vars["item"]->type =		$app->request->query["item_type"];
			$app->vars["item"]->menu =		$app->request->query["item_menu"];
			$app->vars["item"]->parent =	$app->request->query["item_parent"];
			$app->vars["item"]->ord =		$app->request->query["item_ord"];
			$app->vars["item"]->created =	time();
			$app->vars["item"]->modified =	time();

			$app("menu_items")->set($app->vars["item"]->id, $app->vars["item"]);

			$app->redirectWithMessages("/backend/menu_items/editor?edit_id=" . $app->vars["item"]->id, array("type" => "success", "content" => $app("i18n")->getString("changes_saved")));
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
						<label for="item_label"><?php $app("i18n")("item_label_c"); ?></label>
						<input type="text" class="form-control" name="item_label" value="<?php echo $app->vars["item"]->label; ?>" required />

						<label for="item_type"><?php $app("i18n")("item_type_c"); ?></label>
						<select id="item_type" class="form-select" name="item_type">
							<option value="article" <?php if ($app->vars["item"]->type == "article") echo "selected"; ?>>Article</option>
							<option value="category" <?php if ($app->vars["item"]->type == "category") echo "selected"; ?>>Category</option>
							<option value="search" <?php if ($app->vars["item"]->type == "search") echo "selected"; ?>>Search</option>
							<option value="login" <?php if ($app->vars["item"]->type == "login") echo "selected"; ?>>User Login</option>
							<option value="logout" <?php if ($app->vars["item"]->type == "logout") echo "selected"; ?>>User Logout</option>
							<option value="register" <?php if ($app->vars["item"]->type == "register") echo "selected"; ?>>User Register</option>
							<option value="reset_password" <?php if ($app->vars["item"]->type == "reset_password") echo "selected"; ?>>User Password Reset</option>
							<option value="user_profile" <?php if ($app->vars["item"]->type == "user_profile") echo "selected"; ?>>User Profile</option>
							<option value="url" <?php if ($app->vars["item"]->type == "url") echo "selected"; ?>>Custom URL</option>
						</select>

						<label id="item_content_label" for="item_content"><?php $app("i18n")("item_content_c"); ?></label>
						<input type="text" id="item_content" class="form-control" name="item_content" value="<?php echo $app->vars["item"]->content; ?>" />

						<script>
							function selectChanged() {
								var val = document.getElementById("item_type").value;
								var label = document.getElementById("item_content_label");
								var box = document.getElementById("item_content");
								switch (val) {
									case "article":
										box.style.visibility = "visible";
										label.style.visibility = "visible";
										label.innerHTML = "Article ID:";
										break;
									case "category":
										box.style.visibility = "visible";
										label.style.visibility = "visible";
										label.innerHTML = "Category ID:";
										break;
									case "url":
										box.style.visibility = "visible";
										label.style.visibility = "visible";
										label.innerHTML = "URL:";
										break;
									case "search":
									case "login":
									case "logout":
									case "register":
									case "reset_password":
									case "user_profile":
										box.style.visibility = "hidden";
										label.style.visibility = "hidden";
										break;
									default:
										box.style.visibility = "visible";
										label.style.visibility = "visible";
										label.innerHTML = "Item Content:";
								}
							}

							setTimeout(selectChanged, 200);
							document.getElementById("item_type").onchange = selectChanged;
						</script>
					</div>
					<div class="col-12 col-lg-4 ps-lg-2">
						<label for="item_id"><?php $app("i18n")("item_id_c"); ?></label>
						<input type="text" class="form-control" name="item_id" placeholder="auto-generate" value="<?php echo $app->vars["item"]->id; ?>" />

						<label for="item_menu"><?php $app("i18n")("item_menu_c"); ?></label>
						<?php \Crispage\Helpers\RenderHelper::renderMenuPicker("item_menu", $app->vars["item"]->menu); ?>

						<label for="item_parent"><?php $app("i18n")("item_parent_c"); ?></label>
						<?php \Crispage\Helpers\RenderHelper::renderMenuItemPicker("item_parent", $app->vars["item"]->parent, array("title" => "[none]", "value" => "")); ?>

						<label for="item_ord"><?php $app("i18n")("item_order_c"); ?></label>
						<input type="number" class="form-control" name="item_ord" value="<?php echo $app->vars["item"]->ord; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/list" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("back"); ?></a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);"><?php $app("i18n")("save"); ?></button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menu_items.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
