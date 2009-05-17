<?php
    class WaxTextArea extends WaxFormElement implements iBindableControl {
        function __construct() {
            parent::__construct("textarea");
            $this->_allow = array("DefaultValue");
            $this->_passthru["ID"] = "name";
            $this->_passthru["Rows"] = "rows";
            $this->_passthru["Columns"] = "cols";
        }
        function Build() {
            $this->value = (!$this->value ? $this->xmlattributes['DefaultValue'] : $this->value);
        }
        function GetValue() {
            return $this->GetHTML();
        }
        function SetValue($var) {
            $this->SetHTML($var);
        }
    }
?>