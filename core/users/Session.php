<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/Session.php - Session class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Session extends Asset {
		public string $user;
		public string $ip;
		public int $lastactive;

		public function __construct(array $data) {
			parent::__construct("Session", $data);
			if (!is_array($data)) return;
			$this->user = $data["user"] ?? "";
			$this->ip = $data["ip"] ?? "0.0.0.0";
			$this->lastactive = $data["lastactive"] ?? 0;
		}
	}
?>
