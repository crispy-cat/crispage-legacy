<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/views/menu_items/editor.php - Backend menu item editor

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/backend/header.php";

	if (!User::userHasPermissions(Session::getCurrentSession()->user, UserPermissions::MODIFY_MENUS))
		$app->redirectWithMessages("/backend/menu_items/list", array("type" => "error", "content" => "You do not have permission to modify menu items"));

	function checkQuery() {
		global $app;

		return	isset($app->request->query["item_label"]) &&
				isset($app->request->query["item_id"]) &&
				isset($app->request->query["item_type"]) &&
				isset($app->request->query["item_menu"]) &&
				isset($app->request->query["item_parent"]) &&
				isset($app->request->query["item_ord"]);
	}

	$app->vars["item_label"]	= "";
	$app->vars["item_content"]	= "";
	$app->vars["item_id"]		= "";
	$app->vars["item_type"]		= "article";
	$app->vars["item_menu"]		= null;
	$app->vars["item_parent"]	= null;
	$app->vars["item_ord"]		= 0;

	if (isset($app->request->query["edit_id"])) {
		if (!$app("menu_items")->exists($app->request->query["edit_id"]))
			$app->redirectWithMessages("/backend/menu_items/list", array("type" => "error", "content" => "Menu item does not exist"));

		$item = $app("menu_items")->get($app->request->query["edit_id"]);

		if (checkQuery()) {
			$id = $app->request->query["edit_id"];
			if ($app->request->query["edit_id"] != $app->request->query["item_id"]) {
				if ($app("menu_items")->exists($app->request->query["item_id"])) {
					$app->page->alerts["id_taken"] = array("class" => "warning", "content" => "The ID '{$app->request->query["item_id"]}' is taken! Using '$id'.");
				} else {
					if ($app->request->query["item_id"] == "")
						$id = $app->nameToId($app->request->query["item_label"]);
					else
						$id = $app->nameToId($app->request->query["item_id"]);

					$app("menu_items")->delete($app->request->query["edit_id"]);
				}
			}

			$parent = $app->request->query["item_parent"];
			if ($parent == $id) {
				$parent = null;
				$app->page->alerts["parent_self"] = array("class" => "warning", "content" => "Parent item cannot be self.");
			}

			$item->label	= $app->request->query["item_label"];
			$item->content	= $app->request->query["item_content"] ?? "";
			$item->type		= $app->request->query["item_type"];
			$item->id		= $id;
			$item->menu		= $app->request->query["item_menu"];
			$item->parent	= $parent;
			$item->ord		= $app->request->query["item_ord"];
			$item->modified	= time();

			$app("menu_items")->set($id, $item);

			if ($app->request->query["item_id"] == "")
				$app->redirectWithMessages("/backend/menu_items/editor?edit_id=$id", array("type" => "success", "content" => "Changes saved."));

			$app->page->alerts["edit_success"] = array("class" => "success", "content" => "Changes saved.");
		}

		$app->vars["title"] = "Edit Menu Item";

		$app->vars["item_label"]	= htmlentities($item->label);
		$app->vars["item_content"]	= htmlentities($item->content);
		$app->vars["item_id"]		= $item->id;
		$app->vars["item_type"]		= htmlentities($item->type);
		$app->vars["item_menu"]		= htmlentities($item->menu);
		$app->vars["item_parent"]	= htmlentities($item->parent);
		$app->vars["item_ord"]		= htmlentities($item->ord);
	} else {
		$app->vars["title"] = "New Menu Item";

		if (checkQuery()) {
			if ($app->request->query["item_id"] == "")
				$id = $app->nameToId($app->request->query["item_label"]);
			else
				$id = $app->nameToId($app->request->query["item_id"]);

			while ($app("menu_items")->exists($id)) $id .= "_1";

			$id = $app->nameToId($id);

			$item = new MenuItem(array(
				"id"		=> $id,
				"label"		=> $app->request->query["item_label"],
				"content"	=> $app->request->query["item_content"] ?? "",
				"type"		=> $app->request->query["item_type"],
				"menu"		=> $app->request->query["item_menu"],
				"parent"	=> $app->request->query["item_parent"],
				"ord"		=> $app->request->query["item_ord"],
				"created"	=> time(),
				"modified"	=> time()
			));

			$app("menu_items")->set($id, $item);

			$app->redirectWithMessages("/backend/menu_items/editor?edit_id=$id", array("type" => "success", "content" => "Changes saved."));
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
						<label for="item_label">Item Label:</label>
						<input type="text" class="form-control" name="item_label" value="<?php echo $app->vars["item_label"]; ?>" required />

						<label for="item_type">Item Type:</label>
						<select id="item_type" class="form-select" name="item_type">
							<option value="article" <?php if ($app->vars["item_type"] == "article") echo "selected"; ?>>Article</option>
							<option value="category" <?php if ($app->vars["item_type"] == "category") echo "selected"; ?>>Category</option>
							<option value="search" <?php if ($app->vars["item_type"] == "search") echo "selected"; ?>>Search</option>
							<option value="login" <?php if ($app->vars["item_type"] == "login") echo "selected"; ?>>User Login</option>
							<option value="logout" <?php if ($app->vars["item_type"] == "logout") echo "selected"; ?>>User Logout</option>
							<option value="register" <?php if ($app->vars["item_type"] == "register") echo "selected"; ?>>User Register</option>
							<option value="reset_password" <?php if ($app->vars["item_type"] == "reset_password") echo "selected"; ?>>User Password Reset</option>
							<option value="user_profile" <?php if ($app->vars["item_type"] == "user_profile") echo "selected"; ?>>User Profile</option>
							<option value="url" <?php if ($app->vars["item_type"] == "url") echo "selected"; ?>>Custom URL</option>
						</select>

						<label id="item_content_label" for="item_content">Item Content:</label>
						<input type="text" id="item_content" class="form-control" name="item_content" value="<?php echo $app->vars["item_content"]; ?>" />

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
						<label for="item_id">Item ID:</label>
						<input type="text" class="form-control" name="item_id" placeholder="auto-generate" value="<?php echo $app->vars["item_id"]; ?>" />

						<label for="item_menu">Item Menu:</label>
						<?php RenderHelper::renderMenuPicker("item_menu", $app->vars["item_menu"]); ?>

						<label for="item_parent">Item Parent:</label>
						<?php RenderHelper::renderMenuItemPicker("item_parent", $app->vars["item_parent"], array("title" => "[none]", "value" => "")); ?>

						<label for="item_ord">Item Order:</label>
						<input type="number" class="form-control" name="item_ord" value="<?php echo $app->vars["item_ord"]; ?>" />

						<a class="btn btn-secondary btn-lg mt-3 pe-2" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/list" style="width: calc(50% - 0.375rem);">Back</a>
						<button class="btn btn-success btn-lg mt-3" type="submit" style="width: calc(50% - 0.375rem);">Save</button>
					</div>
				</div>
			</form>
		</div>
<?php
	});

	$app->events->trigger("backend.view.menu_items.editor", $app->request->query["edit_id"] ?? null);

	$app->renderPage();
?>
