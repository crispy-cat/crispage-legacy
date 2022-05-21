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
		public int $rank;
		public int $permissions;

		public function __construct(array $data) {
			parent::__construct("UserGroup", $data);
			if (!is_array($data)) return;
			$this->name = $data["name"] ?? "";
			$this->parent = $data["parent"] ?? "";
			$this->rank = $data["rank"] ?? 0;
			$this->permissions = $data["permissions"] ?? 0;
		}

		public static function getGroupPermissions(string $id = null) : int {
			global $app;
			$group = $app("usergroups")->get($id);
			if (!$group) return UserPermissions::NO_PERMISSIONS;
			if ($group->parent) return $group->permissions | self::getGroupPermissions($group->parent);
			else return $group->permissions;
		}

		public static function getGroupRank(string $id = null) : int {
			global $app;
			$group = $app("usergroups")->get($id);
			if (!$group) return 0;
			if ($group->rank < 0) $group->rank = PHP_INT_MAX;
			if ($group->parent) return max($group->rank, self::getGroupRank($group->parent));
			else return $group->rank;
		}
	}
?>
