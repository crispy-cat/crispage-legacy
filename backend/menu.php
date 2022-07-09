<?php
	/*
		Crispage - A lightweight CMS for developers
		backend/menu.php - Backend menu

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.6.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
?>
<ul class="navbar-nav">
	<?php
		$menuitems = $app->getBackendMenuItems();
		usort($menuitems, function($a, $b) {
			return ($b->ord - $a->ord) <=> 0;
		});

		foreach ($menuitems as $item) {
			if ($item->parent) continue;

			$children = array();
			foreach ($menuitems as $citem) {
				if ($citem->parent == $item->id)
					array_push($children, $citem);
			}

			echo "<li class=\"nav-item";
			if (count($children)) echo " dropdown";
			echo "\">";

			$class = "nav-link";
			if ($item->url == $app->request->slug) $class .= " active";
			if (count($children)) $class .= " dropdown-toggle";
			echo "<a class=\"$class\" href=\"" . \Config::WEBROOT . $item->url . "\"";
			if (count($children)) echo " role=\"button\" data-bs-toggle=\"dropdown\"";
			echo ">$item->label</a>\n";
			if (count($children)) {
				echo "<ul class=\"dropdown-menu\">\n";
				foreach ($children as $child)
					echo "<li><a class=\"dropdown-item\" href=\"" . \Config::WEBROOT . $child->url . "\">$child->label</a></li>\n";
				echo "</ul>\n";
			}
			echo "</li>\n";
		}
	?>
</ul>
<ul class="navbar-nav ms-auto">
	<li class="nav-item">
		<a class="nav-link" href="<?php echo \Config::WEBROOT; ?>/" target="_blank"><i class="bi bi-display"></i> View Site</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo \Config::WEBROOT; ?>/installer" target="_blank"><i class="bi bi-gear"></i> Install & Manage</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo \Config::WEBROOT; ?>/logout"><i class="bi bi-door-open"></i> Logout</a>
	</li>
</ul>
