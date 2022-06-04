<?php
	/*
		Crispage - A lightweight CMS for developers
		core/helpers/RenderHelper.php - Render helper

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Helpers;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class RenderHelper {
		public static function renderPagination(string $baseurl, int $npages, int $page) {
?>
			<nav>
				<ul class="pagination">
					<li class="page-item"><a class="page-link" href="<?php echo $baseurl; ?>1"><i class="bi bi-chevron-bar-left"></i></a></li>
					<?php if ($page > 1) { ?>
						<li class="page-item"><a class="page-link" href="<?php echo $baseurl . ($page - 1); ?>"><i class="bi bi-chevron-left"></i></a></li>
					<?php } ?>
					<?php if ($page > 2) { ?>
						<li class="page-item"><a class="page-link" href="<?php echo $baseurl . ($page - 2); ?>"><?php echo $page - 2; ?></a></li>
					<?php } ?>
					<?php if ($page > 1) { ?>
						<li class="page-item"><a class="page-link" href="<?php echo $baseurl . ($page - 1); ?>"><?php echo $page - 1; ?></a></li>
					<?php } ?>
					<li class="page-item active"><a class="page-link" href="#"><?php echo $page; ?></a></li>
					<?php if ($page < $npages) { ?>
						<li class="page-item"><a class="page-link" href="<?php echo $baseurl . ($page + 1); ?>"><?php echo $page + 1; ?></a></li>
					<?php } ?>
					<?php if ($page < $npages - 1) { ?>
						<li class="page-item"><a class="page-link" href="<?php echo $baseurl . ($page + 2); ?>"><?php echo $page + 2; ?></a></li>
					<?php } ?>
					<?php if ($page < $npages) { ?>
						<li class="page-item"><a class="page-link" href="<?php echo $baseurl . ($page + 1); ?>"><i class="bi bi-chevron-right"></i></a></li>
					<?php } ?>
					<li class="page-item"><a class="page-link" href="<?php echo $baseurl . $npages; ?>"><i class="bi bi-chevron-bar-right"></i></a></li>
				</ul>
			</nav>
<?php
		}

		public static function renderArticlePicker(string $selname, string $selart = null, array $extra = null) {
			global $app;
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("articles")->getAll(null, "title") as $art) {
						if ($selart) {
				?>
							<option value="<?php echo $art->id; ?>" <?php if ($art->id == $selart) echo "selected"; ?>><?php echo $art->title; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $art->id; ?>"><?php echo $art->title; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderCategoryPicker(string $selname, string $selcat = null, array $extra = null) {
			global $app;
			// TODO nest the categories
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("categories")->getAll(null, "title") as $cat) {
						if ($selcat) {
				?>
							<option value="<?php echo $cat->id; ?>" <?php if ($cat->id == $selcat) echo "selected"; ?>><?php echo $cat->title; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $cat->id; ?>"><?php echo $cat->title; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderCommentPicker(string $selname, string $selcomm = null, array $extra = null) {
			global $app;
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("comments")->getAll(null, "modified", true) as $comm) {
						if ($selcomm) {
				?>
							<option value="<?php echo $comm->id; ?>" <?php if ($comm->id == $selcomm) echo "selected"; ?>><?php echo $comm->title; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $comm->id; ?>"><?php echo $comm->title; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderMenuPicker(string $selname, string $selmenu = null, array $extra = null) {
			global $app;
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("menus")->getAll(null, "title") as $menu) {
						if ($selmenu) {
				?>
							<option value="<?php echo $menu->id; ?>" <?php if ($menu->id == $selmenu) echo "selected"; ?>><?php echo $menu->title; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $menu->id; ?>"><?php echo $menu->title; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderMenuItemPicker(string $selname, string $selitem = null, array $extra = null) {
			global $app;
			// TODO: Nest items
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("menu_items")->getAll(null, "menu") as $item) {
						if ($selitem) {
				?>
							<option value="<?php echo $item->id; ?>" <?php if ($item->id == $selitem) echo "selected"; ?>><?php echo $item->label; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $item->id; ?>"><?php echo $item->label; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderModulePicker(string $selname, string $selmod = null, array $extra = null) {
			global $app;
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("modules")->getAll(null, "pos") as $mod) {
						if ($selmod) {
				?>
							<option value="<?php echo $mod->id; ?>" <?php if ($mod->id == $selmod) echo "selected"; ?>><?php echo $mod->title; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $mod->id; ?>"><?php echo $mod->label; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderPluginPicker(string $selname, string $selplug = null, array $extra = null) {
			global $app;
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("plugins")->getAll(null, "class") as $plug) {
						if ($selplug) {
				?>
							<option value="<?php echo $plug->id; ?>" <?php if ($plug->id == $selplug) echo "selected"; ?>><?php echo $plug->title; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $plug->id; ?>"><?php echo $plug->label; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderUserPicker(string $selname, string $seluser = null, array $extra = null) {
			global $app;
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("users")->getAll(null, "modified", true) as $user) {
						if ($seluser) {
				?>
							<option value="<?php echo $user->id; ?>" <?php if ($user->id == $seluser) echo "selected"; ?>><?php echo $user->name; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $user->id; ?>"><?php echo $user->label; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderUserGroupPicker(string $selname, string $selgroup = null, array $extra = null) {
			global $app;
			// TODO: Nest groups
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("usergroups")->getAll(null, "rank", true) as $group) {
						if ($selgroup) {
				?>
							<option value="<?php echo $group->id; ?>" <?php if ($group->id == $selgroup) echo "selected"; ?>><?php echo $group->name; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $group->id; ?>"><?php echo $group->name; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderBanPicker(string $selname, string $selban = null, array $extra = null) {
			global $app;
?>
			<select class="form-select" name="<?php echo $selname; ?>">
				<?php if ($extra != null) { ?>
					<option value="<?php echo $extra["value"]; ?>"><?php echo $extra["title"]; ?></option>
				<?php } ?>
				<?php
					foreach ($app("bans")->getAll(null, "expires", true) as $ban) {
						if ($selban) {
				?>
							<option value="<?php echo $ban->id; ?>" <?php if ($ban->id == $selban) echo "selected"; ?>><?php echo $ban->name; ?></option>
				<?php
						} else {
				?>
							<option value="<?php echo $ban->id; ?>"><?php echo $ban->name; ?></option>
				<?php
						}
					}
				?>
			</select>
<?php
		}

		public static function renderBooleanField(string $name, bool $value = false) {
			echo "<br /><div class=\"form-check form-check-inline\">";
			echo "<input type=\"radio\" class=\"form-check-input\" name=\"$name\" id=\"{$name}_true\" value=\"1\"";
			if ($value ?? false) echo " checked";
			echo ">";
			echo "<label class=\"form-check-label\" for=\"{$name}_true\"></label>";
			echo "</div>";
			echo "<div class=\"form-check form-check-inline\">";
			echo "<input type=\"radio\" class=\"form-check-input\" name=\"$name\" id=\"{$name}_false\" value=\"0\"";
			if (!($value ?? false)) echo " checked";
			echo ">";
			echo "<label class=\"form-check-label\" for=\"{$name}_false\">No</label>";
			echo "</div><br />";
		}

		public static function renderYesNo(string $name, string $value = "no") {
?>
			<select class="form-control" name="<?php echo $name; ?>">
				<?php if ($value == "yes") { ?>
					<option value="yes" selected>Yes</option>
					<option value="no">No</option>
				<?php } else { ?>
					<option value="yes">Yes</option>
					<option value="no" selected>No</option>
				<?php } ?>
			</select>
<?php
		}

		public static function renderEditor(string $name, string $value = "") {
			echo "<textarea class=\"editor\" id=\"editor_$name\" name=\"$name\">$value</textarea>";
		}

		public static function renderField(string $name, string $type = "string", string $value = null) {
			switch ($type) {
				case "article":
					self::renderArticlePicker($name, $value ?? null);
					break;
				case "category":
					self::renderCategoryPicker($name, $value ?? null);
					break;
				case "comment":
					self::renderCommentPicker($name, $value ?? null);
					break;
				case "menu":
					self::renderMenuPicker($name, $value ?? null);
					break;
				case "menu_item":
					self::renderMenuItemPicker($name, $value ?? null);
					break;
				case "module":
					self::renderModulePicker($name, $value ?? null);
					break;
				case "plugin":
					self::renderPluginPicker($name, $value ?? null);
					break;
				case "user":
					self::renderUserPicker($name, $value ?? null);
					break;
				case "usergroup":
					self::renderUserGroupPicker($name, $value ?? null);
					break;
				case "ban":
					self::renderBanPicker($name, $value ?? null);
					break;
				case "boolean":
					self::renderBooleanField($name, $value ?? false);
					break;
				case "yesno":
					self::renderYesNo($name, $value ?? "no");
					break;
				case "number":
					echo "<input type=\"number\" class=\"form-control\" name=\"$name\" value=\"" . ($value ?? 0) . "\" required />";
					break;
				case "longtext":
					echo "<textarea class=\"form-control\" name=\"$name\" value=\"" . ($value ?? "") . "\" required /></textarea>";
					break;
				case "editor":
					self::renderEditor($name, $value ?? "");
					break;
				case "string":
				default:
					echo "<input type=\"text\" class=\"form-control\" name=\"$name\" value=\"" . ($value ?? "") . "\" required />";
					break;
			}
		}
	}
?>
