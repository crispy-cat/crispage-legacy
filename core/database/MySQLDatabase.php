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
			
			$this->query["write_row"] = "REPLACE INTO `%s` (%s) VALUES (%s);";

			self::init($this->dsn, $this->username, $this->password);
		}
	}
?>
