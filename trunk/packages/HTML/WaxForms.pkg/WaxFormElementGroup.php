<?php
    class WaxFormElementGroup extends WaxFormElement {
        function __construct() {
            parent::__construct("div");
            $this->_allow = array("BreakAfter");
        }
        function Build() {
            $children = $this->GetChildren();
            $this->ClearChildren();
            $breakat = ($this->xmlattributes['BreakAfter'] ? $this->xmlattributes['BreakAfter'] : 3);
            
            for ($x = 0; $x < count($children); $x++) {
                if ($x != 0 && $x % $breakat == 0) 
                    $this->AddChild(new WaxWebControl("br"));
                
                // make sure each one is a member of the right radio button group
                $children[$x]->htmlattributes['name'] = $this->xmlattributes["ID"];
                
                // readd the child
                $this->AddChild($children[$x]);
            }
        }
    }
?>