<?php
	require_once("WaxWebControl.php");
	
	class ScriptTag extends WaxWebControl {
		function __construct($src, $type = "text/javascript", $language="JavaScript") {
			parent::__construct("script");
			
			$this->htmlattributes['src'] = $src;
			$this->htmlattributes['type'] = $type;
			$this->htmlattributes['language'] = $language;
		}
	}
	
	class Stylesheet extends WaxWebControl {
		function __construct($link, $type = "text/css", $rel = "stylesheet") {
			parent::__construct("link");
			
			$this->htmlattributes['href'] = $link;
			$this->htmlattributes['type'] = $type;
			$this->htmlattributes['rel'] = $rel;
		}
	}
?>