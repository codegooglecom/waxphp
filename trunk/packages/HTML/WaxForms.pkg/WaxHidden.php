<?php
    class WaxHidden extends WaxFormElement implements iBindableControl {
        function __construct() {
            parent::__construct("input");
            $this->_passthru["ID"] = "name";
        }
        function Build() {
            $this->htmlattributes['type'] = "hidden";
            $this->ClearChildren();
        }

        function GetValue() {
            return $this->htmlattributes['value'];
        }
        function SetValue($var) {
            $this->htmlattributes['value'] = $var;
        }
    }
?>