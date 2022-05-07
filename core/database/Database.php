<?php
	/*
		Crispage - A lightweight CMS for developers
		core/database/Database.php - Database class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	abstract class Database {
		public const COLUMN_TYPES = array(
			"array"		=> "array",
			"bool"		=> "bool",
			"boolean"	=> "bool",
			"float"		=> "float",
			"double"	=> "float",
			"real"		=> "float",
			"int"		=> "int",
			"integer"	=> "int",
			"string"	=> "string"
		);
		
		public const COLUMN_INIT = array(
			"array"		=> array(),
			"bool"		=> false,
			"boolean"	=> false,
			"float"		=> 0.0,
			"double"	=> 0.0,
			"real"		=> 0.0,
			"int"		=> 0,
			"integer"	=> 0,
			"string"	=> ""
		);
	
		abstract protected function createTable(string $table, array $cols) : bool;
		abstract protected function dropTable(string $table) : bool;
		abstract protected function purgeTable(string $table) : bool;
		
		abstract protected function addColumn(string $table, string $column, string $type = "string") : bool;
		abstract protected function removeColumn(string $table, string $column) : bool;
		
		abstract protected function readRow(string $table, string $id) : ?array;
		abstract protected function writeRow(string $table, string $id, array $vals) : bool;
		abstract protected function deleteRow(string $table, string $id) : bool;
		abstract protected function existsRow(string $table, string $id) : bool;
		abstract protected function readRows(string $table, array $filters = array(), $ordby = null, $desc = false) : array;
		abstract protected function countRows(string $table) : int;
		
		abstract protected function writeChanges();
	}
?>
