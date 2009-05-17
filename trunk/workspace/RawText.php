<?
	require_once("HTMLControl.php");
	
    class RawText extends HTMLControl {
        protected $_text = '';
        function __construct() {
            parent::__construct("span");
        }
        function Build() {
            if ($this->xmlattributes->Text) 
            	$this->_text = $this->xmlattributes->Text;
        }
        
        // override the actual render function because this shouldn't output a tag -- just text
        // also means we must manually call the ->Build() method
        function Render($return = false) {
            $this->Build();
            if ($return) return $this->_text;
            else echo $this->_text;
        }
    }
    class StringBuffer extends RawText {
    	function __construct($defaultText = '') {
    		parent::__construct();
    		$this->_text = $defaultText;
    	}
    	function Add($txt) {
    		$this->_text .= $txt;
    	}
    	function Clear() {
    		$this->_text = "";
    	}
    }
?>