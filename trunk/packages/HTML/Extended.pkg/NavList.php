<?php
	class NavList extends WaxWebControl {
		function __construct() {
			parent::__construct("ul");
			$this->htmlattributes["class"] = "navlist";
		}
	}
	
	class NavListItem extends WaxWebControl {
		function __construct() {
			parent::__construct("li");
		}
		function PreRender() {
			$link = new WaxWebControl("a",$this->xmlattributes['Text']);
			$link->htmlattributes['href'] = $this->xmlattributes['Link'];
			$link->htmlattributes['class'] = "navlist_link";
				 
			if (strpos($_SERVER['REQUEST_URI'],$this->xmlattributes['Link']) === (strlen($_SERVER['REQUEST_URI']) - strlen($this->xmlattributes['Link']))) {
				$link->htmlattributes["class"] .= " navlist_active";
			}
			
			$this->AddChild($link);
		}
	}
?>