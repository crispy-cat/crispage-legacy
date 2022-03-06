<?php
	/*
		Crispage - A lightweight CMS for developers
		core/events/EventManager.php - Event manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/events/EventAction.php";

	class EventManager {
		private array $eas = array();

		public function trigger(string $event, ...$args) {
			global $app;

			foreach ($this->eas as $ea)
				if ($ea->event == $event) $ea->call($app, ...$args);
		}

		public function registerAction(string $event, EventAction $ea) {
			array_push($this->eas, $ea);
			usort($this->eas, function($a, $b) {
				if ($a->priority == $b->priority) return 0;
				return ($a->priority > $b->priority) ? 1 : -1;
			});
		}

		public function deleteAction(string $id) {
			foreach ($this->eas as $ind => $ea)
				if ($ea->id == $id) array_splice($this->eas, $ind, 1);
		}
	}
?>
