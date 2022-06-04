<?php
	/*
		Crispage - A lightweight CMS for developers
		core/database/MySQLDatabase.php - MySQL Database implementation

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.12.0
	*/

	namespace Crispage\Database;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class MySQLDatabase extends PDODatabase {
		public function __construct(string $loc, string $name, array $options) {
			$this->dsn = "mysql:host=$loc;port=3306;dbname=$name";
			$this->username = $options["USERNAME"] ?? null;
			$this->password = $options["PASSWORD"] ?? null;

			self::init($this->dsn, $this->username, $this->password);
		}

		public function writeRow(string $table, string $id, array $vals) : bool {
			$vals["id"] = $id;
			foreach ($vals as $key => $val) if ($val === null || $val === array()) unset($vals[$key]);
			$qcols = array_keys($vals);
			$qvals = array_map(array($this->pdo, "quote"), array_map(function($val) {
				if (is_array($val)) return "#_ARRAY_#" . json_encode($val);
				return $val;
			}, array_values($vals)));
			$query = "REPLACE INTO `$table` (`" . implode("`, `", $qcols) . "`) VALUES (" . implode(", ", $qvals) . ");";
			$result = $this->tryExec($query);
			return $result != false;
		}
	}
?>
