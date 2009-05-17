<?php
	require_once("WaxControl.php");
	
	class Import extends WaxControl {
		function __construct() {
			parent::__construct("Uses");
		}
		function OnConstruct() {
			Wax::Import($this->xmlattributes['Package']);
		}
	}
?>