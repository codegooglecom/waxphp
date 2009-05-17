<?php
	class NavList extends WaxWebControl {
		function __construct() {
			parent::__construct("ul");
			$this->htmlattributes["class"] = "wnavlist";
		}
	}
	
	class NavListItem extends WaxWebControl {
		function __construct() {
			parent::__construct("li");
		}
		function PreRender(DOMDocument $doc) {
			$link = new WaxWebControl("a",$this->xmlattributes['Text']);
			$link->htmlattributes['href'] = $this->xmlattributes['Link'];
				 
			if (strpos($_SERVER['REQUEST_URI'],$this->xmlattributes['Link']) === (strlen($_SERVER['REQUEST_URI']) - strlen($this->xmlattributes['Link']))) {
				$link->htmlattributes["class"] = (isset($link->htmlattributes["class"]) ? $link->htmlattributes['class'] : '') . " wnavlist_active";
			}
			
			$this->AddChild($link);
		}
	}
?>