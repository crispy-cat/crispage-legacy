<?php
	/*
		Crispage - A lightweight CMS for developers
		core/events/EventManager.php - Event manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	namespace Crispage\Events;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once \Config::APPROOT . "/core/events/EventAction.php";

	class EventManager {
		private array $eas = array();

		public function trigger(string $event, ...$args) {
			global $app;

			foreach ($this->eas as $ea)
				if ($ea->event == $event) $ea->call($app, ...$args);
		}

		public function registerAction(EventAction $ea) {
			array_push($this->eas, $ea);
			usort($this->eas, function($a, $b) {
				return ($a->priority - $b->priority) <=> 0;
			});
		}

		public function deleteAction(string $id) {
			foreach ($this->eas as $ind => $ea)
				if ($ea->id == $id) array_splice($this->eas, $ind, 1);
		}
	}
?>
