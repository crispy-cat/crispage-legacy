<?php
	/*
		Crispage - A lightweight CMS for developers
		core/database/Database.php - Database class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	abstract class Database {
		abstract protected function createTable(string $table, array $cols) : bool;
		abstract protected function dropTable(string $table) : bool;
		abstract protected function purgeTable(string $table) : bool;

		abstract protected function readRow(string $table, string $id) : ?array;
		abstract protected function writeRow(string $table, string $id, array $vals) : bool;
		abstract protected function deleteRow(string $table, string $id) : bool;
		abstract protected function existsRow(string $table, string $id) : bool;
		abstract protected function readRows(string $table, array $filters = array(), $ordby = null, $desc = false) : array;
		
		abstract protected function writeChanges();
	}
?>
