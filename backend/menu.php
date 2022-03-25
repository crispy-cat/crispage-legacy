<ul class="navbar-nav me-auto">
	<li class="nav-item">
		<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/backend/dashboard"><i class="bi bi-clipboard-data"></i> Dashboard</a>
	</li>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
			<i class="bi bi-file-richtext"></i> Content
		</a>
		<ul class="dropdown-menu">
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/articles/list"><i class="bi bi-files"></i> Articles</a></li>
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/categories/list"><i class="bi bi-folder"></i> Categories</a></li>
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/media"><i class="bi bi-images"></i> Media</a></li>
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/comments/list"><i class="bi bi-chat-left-dots"></i> Comments</a></li>
		</ul>
	</li>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
			<i class="bi bi-menu-app"></i> Menus
		</a>
		<ul class="dropdown-menu">
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/menus/list"><i class="bi bi-menu-app"></i> Menus</a></li>
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/menu_items/list"><i class="bi bi-three-dots-vertical"></i> Menu Items</a></li>
		</ul>
	</li>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
			<i class="bi bi-people"></i> Users
		</a>
		<ul class="dropdown-menu">
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/users/list"><i class="bi bi-person"></i> Manage Users</a></li>
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/usergroups/list"><i class="bi bi-people"></i> Manage Usergroups</a></li>
		</ul>
	</li>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
			<i class="bi bi-plug"></i> Extensions
		</a>
		<ul class="dropdown-menu">
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/modules/list"><i class="bi bi-grid-1x2"></i> Modules</a></li>
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/plugins/list"><i class="bi bi-code"></i> Plugins</a></li>
		</ul>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/backend/settings"><i class="bi bi-sliders"></i> Settings</a>
	</li>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
			<i class="bi bi-question-circle"></i> Help
		</a>
		<ul class="dropdown-menu">
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/about"><i class="bi bi-info-circle"></i> About</a></li>
			<li><a class="dropdown-item" href="<?php echo Config::WEBROOT; ?>/backend/support"><i class="bi bi-life-preserver"></i> Support</a></li>
		</ul>
	</li>
</ul>
<ul class="navbar-nav ms-auto">
	<li class="nav-item">
		<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/" target="_blank"><i class="bi bi-display"></i> View Site</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/installer" target="_blank"><i class="bi bi-gear"></i> Install & Manage</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/logout"><i class="bi bi-door-open"></i> Logout</a>
	</li>
</ul>
