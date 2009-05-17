<?php
	/**
	* The DataSource class is responsible for defining the actions 
	* that a DataSource should be able to perform.
	*/
    abstract class DataSource extends WaxControl {
    	protected $_actions = array("Create","Retrieve","RetrieveAll","Update","Delete");
    
		abstract function Create($args);
		abstract function Retrieve($args);
		abstract function Update($args);
		abstract function Delete($args);
    }
?>