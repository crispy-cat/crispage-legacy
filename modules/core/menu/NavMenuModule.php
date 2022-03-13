<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/menu/NavMenuModule.php - Nav menu module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class NavMenuModule extends Module {
		public function render() {
			global $app;

			echo "<ul class=\"module NavMenuModule module-$this->id {$this->options["classes"]}\">\n";

			$menuitems = $app->menus->getMenuItems($this->options["menu"]);
			usort($menuitems, function($a, $b) {
				if ($a->ord == $b->ord) return 0;
				return ($a->ord < $b->ord) ? -1 : 1;
			});

			foreach ($menuitems as $item) {
				if ($item->parent) continue;

				if ($item->type == "login" && $app->session->getCurrentSession()) continue;
				if ($item->type == "register" && $app->session->getCurrentSession()) continue;
				if ($item->type == "logout" && !$app->session->getCurrentSession()) continue;
				if ($item->type == "reset_password" && $app->session->getCurrentSession()) continue;
				if ($item->type == "user_profile" && !$app->session->getCurrentSession()) continue;

				$children = array();
				foreach ($menuitems as $citem) {
					if ($citem->parent == $item->id)
						array_push($children, $citem);
				}

				echo "<li class=\"nav-item";
				if (count($children)) echo " dropdown";
				echo "\">";

				$class = "nav-link";
				if ($item->getUrl() == $app->request->slug)
					// || is home
					$class .= " active";
				if (count($children)) $class .= " dropdown-toggle";
				echo "<a class=\"$class\" href=\"" . (($item->type == "url") ? "" : Config::WEBROOT . "/") . $item->getUrl() . "\"";
				if (count($children)) echo " role=\"button\" data-bs-toggle=\"dropdown\"";
				echo ">$item->label</a>\n";
				if (count($children)) {
					echo "<ul class=\"dropdown-menu\">\n";
					foreach ($children as $child)
						echo "<li><a class=\"dropdown-item\" href=\"" . (($child->type == "url") ? "" : Config::WEBROOT . "/") . $child->getUrl() . "\">$child->label</a></li>\n";
					echo "</ul>\n";
				}
				echo "</li>\n";
			}

			echo "</ul>\n";
		}
	}
?>
