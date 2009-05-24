<?php
	require_once("WaxControl.php");
	
	class PageActions extends WaxControl {
		private $_actionvar = "action";
		private $_showdefault = true;
	
		function __construct() {
			parent::__construct("PageActions");
		}
		function OnConstruct() {
			if (isset($this->xmlattributes['ActionVar']))
				$this->_actionvar = $this->xmlattributes['ActionVar'];
		}
		function ShouldRender($action) {
			if (empty($action)) {
				if ($this->_showdefault) 
					return true;
			}
			else if (isset($_REQUEST[$this->_actionvar]) && $_REQUEST[$this->_actionvar] == $action) {	
				$this->_showdefault = false;
				return true;
			}

			return false;
		}
	}
	
	class PageAction extends WaxControl {
		function __construct() {
			parent::__construct('PageAction');
		}
		function Render(DOMDocument $doc) {
			if ($this->parentNode->ShouldRender($this->xmlattributes['name'])) {
				return parent::Render($doc);
			}
		}
	}
	
	class DefaultPageAction extends PageAction {
		function __construct() {
			parent::__construct();
			$this->xmlattributes['name'] = '';
		}
	}
?>