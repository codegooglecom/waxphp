<?php
	require_once("WaxTextField.php");
	
	class WaxPassword extends WaxTextField {
    	function __construct() {
    		parent::__construct();
    	}
    	function Build() {
			$this->htmlattributes['type'] = "password";
    	}
    }
?>