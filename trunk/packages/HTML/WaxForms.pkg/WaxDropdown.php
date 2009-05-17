<?php
    require_once("WaxFormElement.php");
    
    class WaxDropdown extends WaxFormElement implements iBindableControl {
        function __construct() {
            parent::__construct("select");
            $this->_allow = array("DefaultValue","DefaultIndex");
        }
        function Build() {
            $cur = 0;
            foreach ($this->GetChildren() as $child) {
                if (($this->xmlattributes['DefaultValue'] && $child->xmlattributes['Text'] == $this->xmlattributes['DefaultValue']) ||
                    ($this->xmlattributes['DefaultIndex'] && $cur == $this->xmlattributes['DefaultIndex'])) {
                    
                    $child->htmlattributes['selected'] = "selected";
                    break;
                }    
                else if ($this->htmlattributes['value'] == $child->htmlattributes['value']) {
                    $child->htmlattributes['selected'] = "selected";
                }
                $cur++;
            }
        }
        function GetValue() {
            return $this->htmlattributes['value']; 
        }
        function SetValue($var) { 
            $this->htmlattributes['value'] = $var; 
        }
    }
?>