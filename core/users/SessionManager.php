<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/Session.php - Session manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	include_once Config::APPROOT . "/core/users/Session.php";

	class SessionManager {
		public function startSession(string $user) {
			global $app;
			$sid = Randomizer::randomString();
			$this->setSession($sid, new Session(array(
				"id" => $sid,
				"user" => $user,
				"ip" => $_SERVER["REMOTE_ADDR"],
				"created" => time(),
				"lastactive" => time()
			)));
			setcookie("session_id", $sid);
			$app->events->trigger("session.session_start", $sid);
		}

		public function getCurrentSession() : ?Session {
			global $app;
			if (!isset($app->request->cookies["session_id"])) return null;
			$session = $this->getSession($app->request->cookies["session_id"]);
			if (!$session || (time() - $session->lastactive >= $app->getSetting("users.session_timeout", 3600))) return null;
			return $session;
		}

		public function refreshCurrentSession() {
			global $app;
			if (!isset($app->request->cookies["session_id"])) return;
			$session = $this->getSession($app->request->cookies["session_id"]);
			if (!$session) return;
			if (time() - $session->lastactive >= $app->getSetting("users.session_timeout", 3600)) return;
			$session->lastactive = time();
			$this->setSession($session->id, $session);
			$app->events->trigger("session.session_refresh", $session->id);
		}

		public function endCurrentSession() {
			global $app;
			if (!isset($app->request->cookies["session_id"])) return;
			$this->deleteSession($app->request->cookies["session_id"]);
			$app->events->trigger("session.session_end", $app->request->cookies["session_id"]);
		}

		public function getSession(string $id = null) : ?Session {
			if ($id == null) return null;
			global $app;

			$session = $app->database->readRow("sessions", $id);
			if (!$session) return null;

			$session = new Session($session);
			return $session;
		}

		public function setSession(string $id, Session $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("sessions", $id, array(
				"user" => $data->user,
				"ip" => $data->ip,
				"created" => $data->created,
				"lastactive" => $data->lastactive
			));

			$app->events->trigger("session.session_set", $id);
		}

		public function deleteSession(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("sessions", $id);
			$app->events->trigger("session.session_delete", $id);
		}

		public function existsSession(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("sessions", $id);
		}
	}
?>
