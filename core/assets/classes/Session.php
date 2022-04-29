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

		public function __construct(array $data) {
			parent::__construct("Session", $data);
			if (!is_array($data)) return;
			$this->user = $data["user"] ?? "";
			$this->ip = $data["ip"] ?? "0.0.0.0";
		}

		public static function startSession(string $user) : void {
			global $app;
			$sid = Randomizer::randomString();
			$app("sessions")->set($sid, new Session(array(
				"id" => $sid,
				"user" => $user,
				"ip" => $_SERVER["REMOTE_ADDR"],
				"created" => time(),
				"modified" => time()
			)));
			$app->page->setCookie("session_id", $sid);
			$app->events->trigger("session.session_start", $sid);
		}

		public static function getCurrentSession() : ?Session {
			global $app;
			if (!isset($app->request->cookies["session_id"])) return null;
			$session = $app("sessions")->get($app->request->cookies["session_id"]);
			if (!$session || (time() - $session->modified >= $app->getSetting("users.session_timeout", 3600))) return null;
			return $session;
		}

		public static function refreshCurrentSession() : void {
			global $app;
			if (!isset($app->request->cookies["session_id"])) return;
			$session = $app("sessions")->get($app->request->cookies["session_id"]);
			if (!$session) return;
			if (time() - $session->modified >= $app->getSetting("users.session_timeout", 3600)) return;
			$session->modified = time();
			$app("sessions")->set($session->id, $session);
			$app->events->trigger("session.session_refresh", $session->id);
		}

		public static function endCurrentSession() : void {
			global $app;
			if (!isset($app->request->cookies["session_id"])) return;
			$app("sessions")->delete($app->request->cookies["session_id"]);
			$app->events->trigger("session.session_end", $app->request->cookies["session_id"]);
		}
	}
?>
