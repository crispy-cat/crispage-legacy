<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/BanManager.php - Ban manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	include_once Config::APPROOT . "/core/users/Ban.php";

	class BanManager {
		public function getBan(string $id = null) : ?Ban {
			if ($id == null) return null;
			global $app;

			$ban = $app->database->readRow("bans", $id);
			if (!$ban) return null;

			$ban = new Ban($ban);
			return $ban;
		}

		public function setBan(string $id, Ban $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("bans", $id, array(
				"user"		=> $data->user,
				"created"	=> $data->created,
				"modified"	=> $data->modified,
				"expires"	=> $data->expires,
				"reason"	=> $data->reason
			));

			$app->events->trigger("bans.ban_set", $id);
		}

		public function deleteBan(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("bans", $id);
			$app->events->trigger("bans.ban_delete", $id);
		}

		public function existsBan(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("bans", $id);
		}

		public function getBans(string $user = null) : array {
			global $app;

			if ($user) $dbbans = $app->database->readRows("bans", array("user" => $user));
			else $dbbans = $app->database->readRows("bans");

			$bans = array();

			foreach ($dbbans as $ban)
				array_push($bans, $this->getBan($ban["id"]));

			return $bans;
		}
	}
?>
