<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/Authenticator.php - Authenticator class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Users;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class Authenticator {
		public function authenticateUser(string $id, string $password) : bool {
			global $app;
			$dpass = $app->database->readRow(\Config::PASSWORD_TABLE, $id);
			if (!is_array($dpass)) return false;
			$match = password_verify($password, $dpass["password"]);

			$app->events->trigger("auth.authenticate", $id, $match);
			return $match;
		}

		public function setPassword(string $id, string $password) {
			global $app;
			$app->database->writeRow(\Config::PASSWORD_TABLE, $id, array("password" => password_hash($password, \Config::PASSWORD_ALGO, \Config::PASSWORD_OPTIONS)));
			$app->events->trigger("auth.password_set", $id);
		}
	}
?>
