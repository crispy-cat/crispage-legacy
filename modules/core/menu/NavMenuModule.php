<?php
	/*
		Crispage - A lightweight CMS for developers
		modules/core/menu/NavMenuModule.php - Nav menu module

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Modules;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class NavMenuModule extends \Crispage\Assets\Module {
		public function render() {
			global $app;

			echo "<ul class=\"module NavMenuModule module-$this->id {$this->options["classes"]}\">\n";

			$menuitems = $app("menu_items")->getAllArr(array("menu" => $this->options["menu"]));
			usort($menuitems, function($a, $b) {
				return ($a->ord - $b->ord) <=> 0;
			});

			foreach ($menuitems as $item) {
				if ($item->parent) continue;

				if ($item->type == "login" && \Crispage\Assets\Session::getCurrentSession()) continue;
				if ($item->type == "register" && \Crispage\Assets\Session::getCurrentSession()) continue;
				if ($item->type == "logout" && !\Crispage\Assets\Session::getCurrentSession()) continue;
				if ($item->type == "reset_password" && \Crispage\Assets\Session::getCurrentSession()) continue;
				if ($item->type == "user_profile" && !\Crispage\Assets\Session::getCurrentSession()) continue;

				$children = array();
				foreach ($menuitems as $citem) {
					if ($citem->parent == $item->id)
						array_push($children, $citem);
				}

				echo "<li class=\"nav-item";
				if (count($children)) echo " dropdown";
				echo "\">";

				$class = "nav-link";
				if ($item->getUrl() == $app->request->slug) $class .= " active";
				if (count($children)) $class .= " dropdown-toggle";
				echo "<a class=\"$class\" href=\"" . (($item->type == "url") ? "" : \Config::WEBROOT . "/") . $item->getUrl() . "\"";
				if (count($children)) echo " role=\"button\" data-bs-toggle=\"dropdown\"";
				echo ">$item->label</a>\n";
				if (count($children)) {
					echo "<ul class=\"dropdown-menu\">\n";
					foreach ($children as $child) {
						if ($child->type == "login" && \Crispage\Assets\Session::getCurrentSession()) continue;
						if ($child->type == "register" && \Crispage\Assets\Session::getCurrentSession()) continue;
						if ($child->type == "logout" && !\Crispage\Assets\Session::getCurrentSession()) continue;
						if ($child->type == "reset_password" && \Crispage\Assets\Session::getCurrentSession()) continue;
						if ($child->type == "user_profile" && !\Crispage\Assets\Session::getCurrentSession()) continue;
						echo "<li><a class=\"dropdown-item\" href=\"" . (($child->type == "url") ? "" : \Config::WEBROOT . "/") . $child->getUrl() . "\">$child->label</a></li>\n";
					}
					echo "</ul>\n";
				}
				echo "</li>\n";
			}

			echo "</ul>\n";
		}
	}
?>
