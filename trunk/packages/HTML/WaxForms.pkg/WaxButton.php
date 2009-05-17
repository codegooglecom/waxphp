<?php
    require_once("WaxFormElement.php");
    
    class WaxButton extends WaxFormElement {
        function __construct() {
            parent::__construct("input");
            $this->htmlattributes['Type'] = "button";
            $this->_passthru["Text"] = "value";
        }
        
        function GetValue() { return $this->htmlattributes['value']; }
        function SetValue($var) { $this->htmlattributes['value'] = $var; }
    }
?>