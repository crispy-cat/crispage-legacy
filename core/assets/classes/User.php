<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/User.php - User class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class User extends Asset {
		public string $name;
		public string $email;
		public string $group;
		public int $loggedin;
		public int $activated;

		public function __construct(array $data) {
			parent::__construct("User", $data);
			if (!is_array($data)) return;
			$this->name		= $data["name"] ?? "";
			$this->email	= $data["email"] ?? "";
			$this->group	= $data["group"] ?? "";
			$this->loggedin = $data["loggedin"] ?? 0;
			$this->activated = $data["activated"] ?? 0;
		}

		public static function userHasPermissions(string $id = null, int $perm = 0) : bool {
			global $app;
			$user = $app("users")->get($id);
			if (!$user) return false;
			return (UserGroup::getGroupPermissions($user->group) & $perm) == $perm;
		}

		public static function getUserRank(string $id = null) : int {
			global $app;
			$user = $app("users")->get($id);
			if (!$user) return 0;
			return UserGroup::getGroupRank($user->group);
		}

		public static function compareUserRank(string $user = null, $target = null) : int {
			if (!$user) return -1;
			if ($target === null) return 1;
			if (is_int($target)) return (self::getUserRank($user) - $target) <=> 0;
			else return (self::getUserRank($user) - self::getUserRank($target)) <=> 0;
		}
	}
?>
