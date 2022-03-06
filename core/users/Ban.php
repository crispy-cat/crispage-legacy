<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/Ban.php - Ban class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Ban {
		public string $id;
		public string $user;
		public int $created;
		public int $modified;
		public int $expires;
		public ?string $reason;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id = $data["id"];
			$this->user = $data["user"];
			$this->created = $data["created"];
			$this->modified = $data["modified"];
			$this->expires = $data["expires"];
			$this->reason = $data["reason"];
		}
	}
?>
