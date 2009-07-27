<?php
	/**
	* These interfaces define the properties of classes (usually models)  that 
	* interact with the database.
	*/
	// define the DBUser role that contains utility functions for escaping and reflecting
	interface DBUser extends Role {}
	
	// define the subroles of DBUser
	interface DBCreator extends DBUser {}
	interface DBReader extends DBUser {}
	interface DBUpdater extends DBUser {}
	interface DBDeleter extends DBUser {}
	
	// and finally define a SuperRole that can perform all the basic roles of a DBUser
	// PHP automatically figures out interface inheritance, so this will reflect how we expect
	interface DBCRUD extends DBUser, DBCreator, DBReader, DBUpdater, DBDeleter {}
	
	
	class DBUserActions {
		static function Escape(DBUser $self, $str) {
			if (is_numeric($str)) return $str;
			else return "'" . mysql_real_escape_string($str) . "'";
		}
		static function ParseTable(DBUser $self) {
			$table = get_class($self);
			$table = str_replace(array("model","Model"), "", $table);
			return $table;
		}
		static function Reflect(DBUser $self) {
			$query = "SHOW COLUMNS FROM " . $self->ParseTable() . ";";
			$result = mysql_query($query) or die("ERROR: " . mysql_error());
			
			$fields = array();
			while ($row = mysql_fetch_assoc($result)) {
				$pieces = array();
				preg_match_all("/^(\w+)(\((\d+)\))*$/",$row['Type'],$pieces);
				$fieldinfo = array(
					'type' => $pieces[1],
					'size' => $pieces[2],
					'null' => $row['Null'],
					'keytype' => $row['Key'],
					'default' => $row['Default'],
					'extra' => $row['Extra']
				);

				$fields[$row['Field']] = $fieldinfo;
			}
			
			return $fields;
		}
	}
	
	// give the roles some actions -- just basic mysql actions for now
	class DBCreatorActions {
		static function Create(DBCreator $self, array $arguments) {
			// grab the table name from $self->model
			$table = $self->ParseTable();
			$query = "INSERT INTO $table (";
			$cols = array();
			foreach (array_keys($arguments) as $key) {
				$cols[] = "`$key`";
			}
			$query .= implode(",",$cols);
			$query .= ") VALUES (";
			$values = array();
			foreach ($arguments as $value) {
				$values[] = $self->Escape($value);
			}
			$query .= implode(",",$values);
			$query .= ");";
						
			mysql_query($query) or die("ERROR: " . mysql_error());
			return mysql_insert_id();
		}
	}
	class DBReaderActions {
		static function Read(DBReader $self, $arguments = NULL) {
			$table = $self->ParseTable();
			$query = "SELECT * FROM $table";
			
			$order = NULL;
			if (isset($arguments['sort'])) {
				$order = $arguments['sort'];
				unset($arguments['sort']);
			}
			$limit = NULL;
			if (isset($arguments['limit'])) {
				$limit = $arguments['limit'];
				unset($arguments['limit']);
			}
			
			// filter arguments
			if (!is_null($arguments) && is_array($arguments) && count($arguments) > 0) {
				$query .= " WHERE ";
				$args = array();
				foreach ($arguments as $arg => $val) {
					$args[] = "`$arg` " . (is_numeric($val) ? " = " : " LIKE ") . " " . $self->Escape($val);
				}
				$query .= implode (" AND ",$args);
			}
			
			// order arguments
			if (is_array($order)) {
				$query .= " ORDER BY " . $order['column'] . " " . $order['direction'];
			}
			
			// limit arguments
			if (is_array($limit)) {
				if (isset($limit['start']))
					$query .= " LIMIT " . $limit['start'] . "," . $limit['count'];
				else	
					$query .= " LIMIT " . $limit['count'];
			}
			
			$query .= ";";
			$result = mysql_query($query) or die("ERROR: " . mysql_error());
			if (mysql_num_rows($result) > 0) {
				$rows = array();
				while ($row = mysql_fetch_assoc($result)) {
					$rows[$row['id']] = $row;
				}
				return $rows;
			}
			else return NULL;
		}
	}
	class DBUpdaterActions {
		static function Update(DBUpdater $self, array $arguments, $id = NULL) {
			$table = $self->ParseTable();
			$query = "UPDATE $table SET ";
			
			if (!isset($id) && isset($arguments['id'])) {
				$id = $arguments['id'];
				unset($arguments['id']);
			}
			if (is_null($id))
				return NULL;
			
			$set = array();
			foreach ($arguments as $col => $value) {
				if (!isset($self->reflection[$col]))
					throw new DBUnknownColumnException($col);
				$set[] = "`$col`=" . $self->Escape($value);
			}
			$query .= implode(",",$set);
			$query .= " WHERE id=$id;";
						
			mysql_query($query) or die("ERROR: " . mysql_error());
			return $id;
		}
	}
	class DBDeleterActions {
		static function Delete(DBDeleter $self, $id) {
			$table = $self->ParseTable();
			$query = "DELETE FROM $table WHERE id=$id;";
			mysql_query($query) or die("ERROR: " . mysql_error());
			return true;
		}
	}
?>