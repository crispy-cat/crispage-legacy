<?php
	/*
		Crispage - A lightweight CMS for developers
		core/events/EventAction.php - Event action class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class EventAction {
		public string $id;
		public string $event;
		public int $priority;
		public $action;

		public function __construct(array $data) {
			if (!is_array($data)) return;
			$this->id = $data["id"];
			$this->event = $data["event"];
			$this->priority = $data["priority"];
			$this->action = $data["action"];
		}

		public function call(...$args) {
			($this->action)(...$args);
		}
	}
?>
