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
	}
?>
