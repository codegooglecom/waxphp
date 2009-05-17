<?php
    class Holder extends HTMLControl {
        function __construct() {
            parent::__construct("div");
            $this->_allow = array();    // don't allow options - this is just a nothing tag
        }
        
        // override the render function to make sure that the parent tag isnt rendered
        function Render($return = false) {
            // override the render function
            $buf = '';
            foreach ($this->GetChildren() as $child) {
                $buf .= $child->Render(true);
            }
            
            if ($return) return $buf;
            else echo $buf;
        }
    }
?>