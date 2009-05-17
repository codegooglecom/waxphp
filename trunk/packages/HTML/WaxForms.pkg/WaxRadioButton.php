<?php
    class WaxRadioButton extends WaxFormElement implements iBindableControl {
        function __construct() {
            parent::__construct("label");
            $this->_allow = array("Text","Value","Checked");
        }
        function Build() {
            $radiobutton = new HTMLControl("input");
            $radiobutton->htmlattributes['value'] = $radiobutton->xmlattributes['Value'];
            
            $this->AddChild($radiobutton);
            
            $rawtext = new WaxTextNode($this->xmlattributes['Text']);
            $this->AddChild($rawtext);
        }
        function SetValue($var) {
            if ($var)
                $this->xmlattributes['Checked'] = "checked";
        }
        function GetValue() {
            return $this->htmlattributes['value'];
        }
    }
?>