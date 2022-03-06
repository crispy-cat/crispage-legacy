<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/UserGroup.php - User group class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class UserGroup {
		public string $id;
		public string $name;
		public ?string $parent;
		public int $permissions;
		public int $created;
		public int $modified;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id = $data["id"];
			$this->name = $data["name"];
			$this->parent = $data["parent"];
			$this->permissions = $data["permissions"];
			$this->created = $data["created"];
			$this->modified = $data["modified"];
		}
	}
?>
