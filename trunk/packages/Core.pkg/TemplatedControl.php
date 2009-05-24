<?php
	require_once("WaxWebControl.php");
	
	/**
	* A templated control allows a control's constructor 
	* to automatically populate the children and build controls
	* based off of XML templates
	*/
	class TemplatedControl extends WaxControl {
		private $_template = NULL;
		
		function __construct($baseTemplate = NULL) {
			if ($baseTemplate == NULL)
				$baseTemplate = get_class($this);
				
			$baseTemplate = WaxConf::LookupPath("fs/package/template",array("package" => "Templated","template" => $baseTemplate));

			if (is_file($baseTemplate))
				$this->_template = Page::LoadTemplate($baseTemplate);
		}
		function Render(DOMDocument $doc) {
			return (!is_null($this->_template) ? $this->_template->Render($doc) : array());
		}
	}
?>	