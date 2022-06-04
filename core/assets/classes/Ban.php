<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/classes/Ban.php - Ban class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Ban extends Asset {
		public string $user;
		public int $expires;
		public ?string $reason;

		public function __construct(array $data) {
			parent::__construct("Ban", $data);
			if (!is_array($data)) return;
			$this->user = (string)($data["user"] ?? "");
			$this->expires = (int)($data["expires"] ?? 0);
			$this->reason = (string)($data["reason"] ?? "No reason specified.");
		}
	}
?>
