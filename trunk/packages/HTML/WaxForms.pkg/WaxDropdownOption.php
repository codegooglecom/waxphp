<?php
    require_once("WaxDropdownOption.php");
    
    class WaxDropdownOption extends WaxFormElement {
        function __construct() {
            parent::__construct("option");
            $this->_allow = array("Text");
            $this->_passthru["Value"] = "value";
        }
        function Build() {
            $this->SetHTML($this->xmlattributes['Text']);
        }
    }
?>