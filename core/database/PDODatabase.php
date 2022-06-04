<?php
	/*
		Crispage - A lightweight CMS for developers
		core/database/PDODatabase.php - PDO Database implementation

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.12.0
	*/

	namespace Crispage\Database;

	defined("CRISPAGE") or die("Application must be started from index.php!");

 	abstract class PDODatabase extends Database {
		public const SQL_COLUMN_TYPES = array(
			"array"		=> "TEXT(65535)",
			"bool"		=> "BOOL",
			"boolean"	=> "BOOL",
			"float"		=> "FLOAT(53)",
			"double"	=> "FLOAT(53)",
			"real"		=> "FLOAT(53)",
			"int"		=> "INT(255)",
			"integer"	=> "INT(255)",
			"string"	=> "TEXT(65535)",
			"id"		=> "VARCHAR(255)"
		);

		protected array $query = array(
			"create_table"	=> "CREATE TABLE `%s` (%s, CONSTRAINT `pk_id` PRIMARY KEY (`id`));",
			"drop_table"	=> "DROP TABLE `%s`;",
			"purge_table"	=> "TRUNCATE TABLE `%s`;",
			"add_column"	=> "ALTER TABLE `%s` ADD COLUMN `%s` %s;",
			"remove_column"	=> "ALTER TABLE `%s` DROP COLUMN `%s`;",
			"read_row"		=> "SELECT * FROM `%s` WHERE `id` = %s;",
			"write_row"		=> "INSERT OR REPLACE INTO `%s` (%s) VALUES (%s);",
			"delete_row"	=> "DELETE FROM `%s` WHERE `id` = %s;",
			"exists_row"	=> "SELECT COUNT(*) AS `count` FROM `%s` WHERE `id` = %s;",
			"g_rows"		=> "SELECT * FROM `%s`;",
			"g_rows_filter"	=> "SELECT * FROM `%s` WHERE %s;",
			"g_rows_order"	=> "SELECT * FROM `%s`ORDER BY %s %s;",
			"g_rows_filter_order"=>"SELECT * FROM `%s` WHERE %s ORDER BY %s %s;",
			"count_rows"	=> "SELECT COUNT(*) AS `count` FROM `%s`;"
		);

		protected string $dsn;
		protected ?string $username;
		protected ?string $password;
		protected bool $unwritten = false;
		public \PDO $pdo;

		protected function init() : void {
			$this->pdo = new \PDO($this->dsn, $this->username, $this->password, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
		}

		public function __destruct() {
			if ($this->unwritten) $this->pdo->rollBack();
			unset($this->pdo);
		}

		public function tryQuery(...$args) : ?\PDOStatement {
			try {
				$result = $this->pdo->query(...$args);
				return $result;
			} catch (\Throwable $e) {
				throw new \Crispage\ApplicationException(500, "Query Error", "Query Error", 1002, $e, false);
			}
		}

		public function tryExec(...$args) : int {
			try {
				$rows = $this->pdo->exec(...$args);
				return $rows;
			} catch (\Throwable $e) {
				throw new \Crispage\ApplicationException(500, "Query Error", "Query Error", 1003, $e, false);
			}
		}

		public function createTable(string $table, array $cols) : bool {
			$cols["id"] = "id";
			$qcols = array();
			foreach ($cols as $col => $type) {
				if ($type == "array") $type == "string";
				$qcols[] = "`$col` " . self::SQL_COLUMN_TYPES[$type] . " DEFAULT '" . self::COLUMN_INIT[$type] . "'";
			}
			$query = sprintf($this->query["create_table"], $table, implode(", ", $qcols));
			return $this->tryExec($query) !== false;
		}

		public function dropTable(string $table) : bool {
			$query = sprintf($this->query["drop_table"], $table);
			return $this->tryExec($query) !== false;
		}

		public function purgeTable(string $table) : bool {
			$query = sprintf($this->query["drop_table"], $table);
			return $this->tryExec($query) !== false;
		}

		public function addColumn(string $table, string $column, string $type = "string") : bool {
			$query = sprintf($this->query["add_column"], $table, $column, self::SQL_COLUMN_TYPES[$type]);
			return $this->tryExec($query) !== false;
		}

		public function removeColumn(string $table, string $column) : bool {
			$query = sprintf($this->query["remove_column"], $table, $column);
			return $this->tryExec($query) !== false;
		}

		public function readRow(string $table, string $id) : ?array {
			$query = sprintf($this->query["read_row"], $table, $this->pdo->quote($id));
			$result = $this->tryQuery($query);
			if (!$result) return null;
			$data = $result->fetch();
			if (!$data) return null;
			return array_map(function($val) {
				if (str_starts_with($val, "#_ARRAY_#")) return json_decode(substr($val, 9), true);
				return $val;
			}, $data);
		}

		public function writeRow(string $table, string $id, array $vals) : bool {
			$vals["id"] = $id;
			foreach ($vals as $key => $val) if ($val === null || $val === array()) unset($vals[$key]);
			$qcols = array_keys($vals);
			$qvals = array_map(array($this->pdo, "quote"), array_map(function($val) {
				if (is_array($val)) return "#_ARRAY_#" . json_encode($val);
				return $val;
			}, array_values($vals)));
			$query = sprintf($this->query["write_row"], $table, "`" . implode("`, `", $qcols) . "`", implode(", ", $qvals));
			$result = $this->tryExec($query);
			return $result != false;
		}

		public function deleteRow(string $table, string $id) : bool {
			$query = sprintf($this->query["delete_row"], $table, $this->pdo->quote($id));
			$result = $this->tryExec($query);
			return $result !== false;
		}

		public function existsRow(string $table, string $id) : bool {
			$query = sprintf($this->query["exists_row"], $table, $this->pdo->quote($id));
			$result = $this->tryQuery($query);
			if (!$result) return false;
			return $result->fetch()["count"] > 0;
		}

		public function readRows(string $table, array $filters = array(), string $ordby = null, bool $desc = false) : array {
			$rows = array();
			foreach ($this->gRows($table, $filters, $ordby, $desc) as $row)
				$rows[] = $row;
			return $rows;
		}

		public function gRows(string $table, array $filters = array(), string $ordby = null, bool $desc = false) : ?\Generator {
			$query = "g_rows";
			$qfilters = array();
			if (!empty($filters)) {
				$query .= "_filter";
				foreach ($filters as $col => $val)
					$qfilters[] = "`$col` = " . $this->pdo->quote($val);
			}
			if ($ordby) $query .= "_order";
			$query = sprintf($this->query[$query], $table, implode(" AND ", $qfilters), $ordby, ($desc ? "DESC" : "ASC"));
			$result = $this->tryQuery($query);
			if (!$result) return null;
			while (($row = $result->fetch()) !== false) {
				yield array_map(function($val) {
					if (str_starts_with($val, "#_ARRAY_#")) return json_decode(substr($val, 9), true);
					return $val;
				}, $row);
			}
		}

		public function countRows(string $table) : int {
			$query = sprintf($this->query["count_rows"], $table);
			$result = $this->tryQuery($query);
			if (!$result) return 0;
			return $result->fetch()["count"];
		}

		public function writeChanges() {
			if ($this->unwritten) {
				$this->unwritten = false;
				$this->pdo->commit();
			}
		}
	}
?>
