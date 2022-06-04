<?php
	/*
		Crispage - A lightweight CMS for developers
		core/database/SQLiteDatabase.php - SQLite Database implementation

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.12.0
	*/

	namespace Crispage\Database;

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class SQLiteDatabase extends PDODatabase {
		public function __construct(string $loc, string $name, array $options) {
			$this->dsn = "sqlite:$loc/$name";
			$this->username = $options["USERNAME"] ?? null;
			$this->password = $options["PASSWORD"] ?? null;

			self::init($this->dsn, $this->username, $this->password);
		}
	}
?>
