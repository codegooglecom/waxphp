<?php
    class WaxForm extends WaxWebControl {
        private $_formchildren = array();
        
        function __construct() {
            parent::__construct("form");

            $this->_passthru["Action"] = "action";
            $this->_passthru["Method"] = "method";
        }
        
        function registerChildren($nodeparent) {
            foreach ($nodeparent->GetChildren() as $child) {
                if ($child instanceof WaxFormElement) {
                	$this->_formchildren[$child->xmlattributes['ID']] = $child;
                	$childID = $child->xmlattributes['ID'];
                	$this->_formchildren[$childID]->htmlattributes['name'] = $this->xmlattributes['ID'] . "_" . $childID;
                }
                if ($child->HasChildren())
                    $this->registerChildren($child);
            }
        }
        
        function OnConstruct($page) {
            $this->registerChildren($this);
            $page->RegisterImportant($this->xmlattributes['ID'],$this);
        }
        
        function Build() {
            if (!$this->htmlattributes['method']) 
                $this->htmlattributes['method'] = "post";
        }
        
        // databinding funcs (sort of)
        // this acts as a portal to handle data from 
        // the data source
        function __get($var) {
        	if (isset($this->_formchildren[$var])) {
        		return $this->_formchildren[$var]->GetValue();
        	}
        }
        function __set($var,$val) {
        	if (isset($this->_formchildren[$var])) {
        		$this->_formchildren[$var]->SetValue($val);
        	}
        }
    }
    
    // a submit and reset button
    class WaxFormControls extends WaxWebControl {
        function __construct() {
            parent::__construct("div");
            $this->_allow = array("Submit","Reset","SubmitText","ResetText");
        }
        function Build() {
            if ($this->xmlattributes['Submit'] != false || $this->xmlattribute['Submit'] === null) {
                $submit = new WaxSubmit();
                $submit->xmlattributes['Text'] = ($this->xmlattributes['SubmitText'] ? $this->xmlattributes['SubmitText'] : "Submit");
                $this->AddChild($submit);
            }
            
            if ($this->xmlattributes->Reset != false || $this->xmlattribute->Submit === null) {
                $reset = new WaxReset();
                $reset->xmlattributes['Text'] = $this->xmlattributes['ResetText'];
                $this->AddChild($reset);
            }
        }
    }
?>