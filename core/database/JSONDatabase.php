<?php
	/*
		Crispage - A lightweight CMS for developers
		core/datebase/JSONDatabase.php - JSON database implementation

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class JSONDatabase extends Database {
		private string $dbfpre;
		private bool $pretty;
		private array $inittypes = array(
			"string" => "",
			"integer" => 0,
			"double" => 0,
			"number" => 0,
			"boolean" => false,
			"array" => array(),
			"null" => null
		);

		private array $dbdata = array();

		public function __construct(string $loc, string $name, bool $pretty) {
			$this->dbfpre = $loc . "/" . $name . "/";
			$this->pretty = $pretty;
		}

		private function getData(string $table) : array {
			if (!isset($this->dbdata[$table])) {
				$file = "$this->dbfpre$table.db.json";
				if (!file_exists($file) || !filesize($file)) throw new Exception("Attempted to read nonexistent database file $file");
				$fc = fopen($file, "r");
				if (!$fc) throw new Exception("Could not open database file for reading $file");
				if (!flock($fc, LOCK_SH)) throw new Exception("Could not get lock on database file $file");
				$data = fread($fc, filesize($file));
				if (!flock($fc, LOCK_UN)) throw new Exception("Could not release lock on database file $file");
				fclose($fc);
				$data = json_decode($data, true);
				if (!$data) throw new Exception("Corrupted database file $file");
				$this->dbdata[$table] = $data;
			}
			return $this->dbdata[$table];
		}

		private function setData(string $table, array $data) {
			$this->dbdata[$table] = $data;
		}

		public function createTable(string $table, array $cols) : bool {
			global $app;
			if (!$table) return false;
			$data = array(
				"#_VERSION_#" => CRISPAGE,
				"TableName" => $table,
				"Columns" => array(),
				"ColumnTypes" => array(),
				"TableData" => array()
			);
			if (!$cols["id"]) $cols["id"] = "string";
			foreach ($cols as $name => $type) {
				array_push($data["Columns"], $name);
				$data["ColumnTypes"][$name] = $type;
			}
			$this->setData($table, $data);
			$app->events->trigger("database.table_create", $table);
			return true;
		}

		public function dropTable(string $table) : bool {
			global $app;
			if (!$table) return false;
			$file = "$this->dbfpre$table.db.json";
			if (!file_exists($file)) return true;
			$res = unlink($file);
			$app->events->trigger("database.table_drop", $table);
			return $res;
		}

		public function purgeTable(string $table) : bool {
			global $app;
			if (!$table) return false;
			$data = $this->getData($table);
			$data["TableData"] = array();
			$this->setData($table, $data);
			$app->events->trigger("database.table_purge", $table);
			return true;
		}

		public function readRow(string $table, string $id) : ?array {
			if (!$table) return null;
			$data = $this->getData($table);
			if (!is_array($data["TableData"])) throw new Exception("Corrupted table $table");
			foreach ($data["TableData"] as $row)
				if ($row["id"] == $id) return $row;
			return null;
		}

		public function writeRow(string $table, string $id, array $vals) : bool {
			global $app;
			if (!$table) return false;
			$data = $this->getData($table);
			if (
				!is_array($data["Columns"]) ||
				!is_array($data["ColumnTypes"]) ||
				!is_array($data["TableData"])
			) throw new Exception("Corrupted table $table");
			$drow = array("id" => $id);
			foreach ($data["Columns"] as $col) {
				// TODO: check type matches
				if (array_key_exists($col, $vals))
					$drow[$col] = $vals[$col];
				else continue;
			}
			$idrow = count($data["TableData"]);
			foreach ($data["TableData"] as $irow => $row) {
				if ($row["id"] == $id)
					$idrow = $irow;}
			$data["TableData"][$idrow] = $drow;
			$this->setData($table, $data);
			$app->events->trigger("database.row_write", $table, $id, $vals);
			return true;
		}

		public function deleteRow(string $table, string $id) : bool {
			global $app;
			if (!$table) return false;
			$data = $this->getData($table);
			if (!is_array($data["TableData"])) throw new Exception("Corrupted table $table");
			foreach ($data["TableData"] as $irow => $row)
				if ($row["id"] == $id)
					array_splice($data["TableData"], $irow, 1);
			$this->setData($table, $data);
			$app->events->trigger("database.row_delete", $table, $id);
			return true;
		}

		public function existsRow(string $table, string $id) : bool {
			if (!$table) return false;
			$data = $this->getData($table);
			if (!is_array($data["TableData"])) throw new Exception("Corrupted table $table");
			foreach ($data["TableData"] as $row)
				if ($row["id"] == $id) return true;
			return false;
		}

		public function readRows(string $table, array $filters = array(), $ordby = null, $desc = false) : array {
			if (!$table) return false;
			$data = $this->getData($table);
			if (!is_array($data["Columns"]) || !is_array($data["TableData"])) throw new Exception("Corrupted table $table");
			$rdata = array();
			if (count($filters)) {
				foreach ($data["TableData"] as $irow => $row) {
					$push = true;
					foreach ($filters as $fcol => $fval)
						$push &= (($row[$fcol] ?? $fval) == $fval);
					if ($push) array_push($rdata, $row);
				}
			} else {
				$rdata = $data["TableData"];
			}

			if ($ordby !== null && in_array($ordby, $data["Columns"])) {
				global $o;
				$o = $ordby;
				usort($rdata, function($a, $b) {
					global $o;
					if ($a[$o] == $b[$o]) return 0;
					if ($a[$o] > $b[$o]) return ($desc) ? -1 : 1;
					return ($desc) ? 1 : -1;
				});
			}
			return $rdata;
		}

		public function writeChanges() {
			global $app;
			$app->events->trigger("database.write_changes");
			foreach ($this->dbdata as $table => $data) {
				$file = "$this->dbfpre$table.db.json";
				$fc = fopen($file, "c+");
				if (!$fc) throw new Exception("Could not open database file for writing $file");
				if (!flock($fc, LOCK_EX)) throw new Exception("Could not get lock on database file $file");
				if ($this->pretty)
					$json = preg_replace("/    /", "\t", json_encode($data, JSON_PRETTY_PRINT));
				else
					$json = json_encode($data);
				if (ftruncate($fc, 0)) {
					fseek($fc, 0);
					if (!fwrite($fc, $json)) throw new Exception("Could not write database file $file");
				} else {
					throw new Exception("Could not truncate database file $file before write");
				}
				if (!flock($fc, LOCK_UN)) throw new Exception("Could not release lock on database file $file");
				fclose($fc);
			}
		}
	}
