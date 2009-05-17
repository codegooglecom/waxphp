<?php
    class WaxReset extends WaxFormElement {
        function __construct() {
            parent::__construct("input");
            $this->_passthru["Text"] = "value";
        }
        function Build() {
            $this->htmlattributes['type'] = "reset";
        }
    }
?>