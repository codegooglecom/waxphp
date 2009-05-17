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
			echo "ShouldRender '$action' <br />";
			if (isset($_REQUEST[$this->_actionvar]) && $_REQUEST[$this->_actionvar] == $action) {	
				$this->_showdefault = false;
				return true;
			}
			else return false;
		}
	}
	
	class PageAction extends WaxControl {
		function __construct() {
			parent::__construct('PageAction');
		}
		function Render(DOMDocument $doc) {
			echo "Should render : " . $this->xmlattributes['name'] . "<br />";
			if ($this->parentNode->ShouldRender($this->xmlattributes['name'])) {
				echo "Calling parent::Render() on action: " . $this->xmlattributes['name'] . "<br />";
				parent::Render($doc);
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