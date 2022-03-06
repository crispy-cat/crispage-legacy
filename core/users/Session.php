<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/Session.php - Session class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Session {
		public string $id;
		public string $user;
		public string $ip;
		public int $started;
		public int $lastactive;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id = $data["id"];
			$this->user = $data["user"];
			$this->ip = $data["ip"];
			$this->started = $data["started"];
			$this->lastactive = $data["lastactive"];
		}
	}
?>
