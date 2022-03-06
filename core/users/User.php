<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/User.php - User class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class User {
		public string $id;
		public string $name;
		public string $email;
		public string $group;
		public int $created;
		public int $modified;
		public int $loggedin;
		public int $activated;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id		= $data["id"];
			$this->name		= $data["name"];
			$this->email	= $data["email"];
			$this->group	= $data["group"];
			$this->created	= $data["created"];
			$this->modified = $data["modified"];
			$this->loggedin = $data["loggedin"];
			$this->activated = $data["activated"];
		}
	}
?>
