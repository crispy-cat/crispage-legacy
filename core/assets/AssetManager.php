<?php
	/*
		Crispage - A lightweight CMS for developers
		core/assets/AssetManager.php - AssetManager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.9.0
	*/

	namespace Crispage\Assets;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once \Config::APPROOT . "/core/assets/Asset.php";

	class AssetManager {
		public string $table;
		public string $class;

		public function __construct(string $table, string $class) {
			if (!is_subclass_of($class, "\Crispage\Assets\Asset"))
				throw new \Crispage\ApplicationException(500, "Asset Error", "$class must be an Asset!", 1005, null, false);
			$this->table = $table;
			$this->class = $class;
		}

		public function __invoke(string $id = null) : ?Asset {
			return $this->get($id);
		}

		public function get(string $id = null) : ?Asset {
			if ($id == null) return null;
			global $app;

			$asset = $app->database->readRow($this->table, $id);
			if (!$asset) return null;

			$class = $this->class;
			return new $class($asset);
		}

		public function set(string $id, Asset $asset) : void {
			global $app;

			$app->events->trigger("assets.$this->table.set.pre", $id);
			$app->database->writeRow($this->table, $id, $asset->toDatabaseObject());
			$app->events->trigger("assets.$this->table.set", $id);
		}

		public function delete(string $id) : void {
			global $app;

			$app->events->trigger("assets.$this->table.delete.pre", $id);
			$app->database->deleteRow($this->table, $id);
			$app->events->trigger("assets.$this->table.delete", $id);
		}

		public function exists(string $id) : bool {
			global $app;

			return $app->database->existsRow($this->table, $id);
		}

		public function getAll(array $filter = null, string $order = null, bool $desc = false) {
			global $app;
			$class = $this->class;

			foreach ($app->database->readRows($this->table, $filter ?? array(), $order, $desc) as $asset)
				yield new $class($asset);
		}

		public function getAllArr(array $filter = null, string $order = null, bool $desc = false) {
			global $app;
			$class = $this->class;

			$assets = array();
			foreach ($app->database->readRows($this->table, $filter ?? array(), $order, $desc) as $asset)
				$assets[] = new $class($asset);

			return $assets;
		}
	}
