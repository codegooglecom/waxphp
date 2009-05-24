<?php
	require_once("WaxControl.php");
	
	// the import object can take CSS and JavaScript tags as children
	class Import extends WaxControl {
		function __construct() {
			parent::__construct("div");
		}
		function OnConstruct() {
			$resources = Wax::Import($this->xmlattributes['Package']);
			foreach ($resources as $type => $resources) {
				if ($type == "js") {
					foreach ($resources as $script) {
						$tag = new ScriptTag($script);
						$this->AddChild($tag);
					}
				}
				else if ($type == "css") {
					foreach ($resources as $css) {
						$tag = new Stylesheet($css);
						$this->AddChild($tag);
					}
				}
			}
		}
	}
?>