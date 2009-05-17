<?php
    class TitledListMenu extends WaxWebControl {
        function __construct() {
            parent::__construct("div");
        }
        function Build($page) {
            // move all of the nodes into a list
            $list = new WaxWebControl("ul");
            foreach ($this->GetChildren() as $child) {
                $list->AddChild($child);
            }
            $this->ClearChildren();
            
            $newnode = new WaxWebControl("h3",$this->xmlattributes->Title);
            $this->AddChild($newnode);
            
            $this->AddChild($list);
        }
    }
    
    class ListMenuItem extends WaxWebControl {
        function __construct() {
            parent::__construct("li");
            $this->_allow = array("Text","Link");
        }
        function Build($page) {
            $a = new WaxWebControl("a",$this->xmlattributes->Text);
            $a->htmlattributes['href'] = $this->xmlattributes['Link'];
            $this->AddChild($a);
        }
    }
?>