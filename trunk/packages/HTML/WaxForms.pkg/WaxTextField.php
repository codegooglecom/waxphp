<?php
    class WaxTextField extends WaxFormElement implements iBindableControl {
        function __construct() {
            parent::__construct("input");
            $this->_allow = array("Size","DefaultValue");
        }
        function Build() {
            $this->htmlattributes->type = "text";
            $this->htmlattributes->size = $this->xmlattributes['Size'];
            if ($this->xmlattributes['DefaultValue']) {
                // TODO: javascript binding to auto clear on click    
                $this->htmlattributes['value'] = $this->xmlattributes['DefaultValue'];
            }
            
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