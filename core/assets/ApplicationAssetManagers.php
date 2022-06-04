<?php
	/*
		Crispage - A lightweight CMS for developers
		core/ApplicationAssetManagers.php - ApplicationAssetManagers class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once \Config::APPROOT . "/core/assets/AssetManager.php";

	class ApplicationAssetManagers {
		private array $ams = array();

		public function __invoke(string $name) {
			return $this->ams[$name];
		}

		public function addAssetManager(string $name, AssetManager $am) {
			$this->ams[$name] = $am;
		}

		public function removeAssetManager(string $name) {
			unset($this->ams[$name]);
		}
	}
?>
