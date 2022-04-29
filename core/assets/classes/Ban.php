<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/Ban.php - Ban class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Ban extends Asset {
		public string $user;
		public int $expires;
		public ?string $reason;

		public function __construct(array $data) {
			parent::__construct("Ban", $data);
			if (!is_array($data)) return;
			$this->user = $data["user"] ?? "";
			$this->expires = $data["expires"] ?? 0;
			$this->reason = $data["reason"] ?? "No reason specified.";
		}
	}
?>
