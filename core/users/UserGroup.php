<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/UserGroup.php - User group class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class UserGroup extends Asset {
		public string $name;
		public ?string $parent;
		public int $permissions;

		public function __construct(array $data) {
			parent::__construct("UserGroup", $data);
			if (!is_array($data)) return;
			$this->name = $data["name"] ?? "";
			$this->parent = $data["parent"] ?? "";
			$this->permissions = $data["permissions"] ?? 0;
		}
	}
?>
