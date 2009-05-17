<?php
	/// Define exceptions for DBDS
	
	class PDOConnectException extends Exception {}
	
	/**
	* The DBDS class is the Database DataSource class.  This class 
	* is responsible for wrapping a PDO connection for use with
	* Service Data Objects.  It makes use of the SDO_DAS_Relational 
	* extension to communicate and maintain information in databases.
	*/
	abstract class DBDS {
		private $_pdo;
		private $_prepared;
		
		function __construct($pdostr,$username,$password) {
			$this->_pdo = new PDO($pdostr,$username,$password);
			if (!$this->_pdo) {
				throw new PDOConnectException("Could not connect to database");
			}
			
			// as a DBDS - it should be defined in XML with the right command attributes
			// take the time to prepare them now.
			foreach ($this->_actions as $action) {
				if ($this->xmlattributes->Exists("{$action}Command")) {
					
				}
			}
		}
		
		function Create($args) {
			
		}
		function Retrieve($args) {
			
		}
		function Update($args) {
			
		}
		function Delete($args) {
			
		}
	}
?>