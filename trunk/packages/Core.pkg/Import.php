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
					// create a CSS aggregator using the current active theme
					if (!is_null(Theme::GetActiveTheme())) {
						foreach ($resources as &$resource) {
							$resource = WaxConf::WEBtoFS($resource);
						}
						
						if (count($resources) > 0) {
							$ag = new CSSAggregator(Theme::GetActiveTheme(),$resources);
							$sstag = new Stylesheet($ag->GetAggregatedStylesheets());
							$this->AddChild($sstag);
						}
					}
					else {
						foreach ($resources as $css) {
							$sstag = new Stylesheet($css);
							$this->AddChild($sstag);
						}
					}
				}
			}
		}
	}
?>