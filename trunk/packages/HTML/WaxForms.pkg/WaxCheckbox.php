<?php
    require_once("WaxFormElement.php");
    
    class WaxCheckbox extends WaxFormElement implements iBindableControl {
        private $_boxref = null;
        
        function __construct() {
            parent::__construct("label");
            $this->_allow = array("Text","Checked");
        }
        function Build() {
            if ($this->xmlattributes['Checked'] == "true")
                $this->htmlattributes['checked'] = "checked";
                
            $cbox = new WaxWebControl("input");
            $cbox->htmlattributes['type'] = "checkbox";
            $cbox->htmlattributes['name'] = $this->xmlattributes['ID'];
            $this->AddChild($cbox);
            
            $rawtext = new WaxTextNode($this->xmlattributes['Text']);
            $this->AddChild($rawtext);
        }
        function GetValue() { 
            return $this->htmlattributes->checked; 
        }
        function SetValue($var) { 
            if ($var) $this->htmlattributes->checked = "checked";
        }
    }
?>