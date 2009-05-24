<?php
	require_once("CSSAggregator.php");
	
	// define the themeconf classes
	class ThemeConf extends WaxControl { 
		private $_cssvars = array();
		
		function AddChild(XMLControl $ctrl) {
			if ($ctrl instanceof CSSVar) {
				$this->_cssvars[$ctrl->xmlattributes["Name"]] = $ctrl->xmlattributes["Value"];
			}
			
			parent::AddChild($ctrl);
		}
		function GetCSSVars() {
			return $this->_cssvars;
		}
	}
	class CSSVar extends WaxControl { }
	
	class Theme extends WaxControl {
		private $_themepath = NULL;
		private $_themeconf = NULL;
		private $_cssaggregator = NULL;
		
		function OnConstruct() {
			if (isset($this->xmlattributes['Name'])) {
				$this->_themepath = WaxConf::LookupPath("fs/theme",array("theme" => $this->xmlattributes['Name']));
				$this->_themeconf = Page::LoadTemplate($this->_themepath . "/theme.waxml");
				
				$this->_cssaggregator = new CSSAggregator($this);
			}
		}
		
		function GetCSSVars() {
			return $this->_themeconf->GetCSSVars();
		}
		
		private function rGetResources($regex, $path = null, $convertForWeb = true) {
			if (is_null($path))
				$path = $this->_themepath;
								
			$base = array();
				
			foreach (scandir($path) as $file) {
				if ($file[0] == '.') continue;
				else if (is_dir("$path/$file")) {
					$base = array_merge($base, $this->rGetResources($regex,"$path/$file", $convertForWeb));
				}
				else if (preg_match($regex,$file)) {
					if ($convertForWeb)
						$base[] = WaxConf::FStoWEB($path . '/' . $file);
					else
						$base[] = "$path/$file";
				}
			}
			
			return $base;
		}
		
		function GetScriptPaths($forWeb = true) {
			$scripts = $this->rGetResources("/\.js$/", WaxConf::LookupPath("fs/theme/scriptdir",array("theme" => $this->xmlattributes['Name'])),$forWeb);
			return $scripts;
		}
		function GetScripts() {
			$ret = array();
			foreach ($this->GetScriptPaths() as $src) {
				$ret[] = new ScriptTag($src);
			}
			return $ret;
		}
		
		/**
		* This function returns the location of the file that has
		* aggregated all of the theme's CSS files and applied the 
		* necessary variables to them.
		*/
		function GetAggregatedStylesheets() {
			$csscache = WaxConf::LookupPath("fs/theme/csscache",array("theme" => $this->xmlattributes["Name"]));
			$newname = basename($this->_cssaggregator->GetTmpName());
			$link = NULL;
			
			// if the cache directory doesn't exist, create it
			if (!is_dir($csscache)) {
				mkdir($csscache, 0600);
			}
			
			// if we have a cache directory, try saving the file there for direct reference
			if (is_dir($csscache)) {
				if (!is_file("$csscache/$newname"))
					rename($this->_cssaggregator->GetTmpName(), $csscache . "/" . $newname);
					
				$link = WaxConf::LookupPath("web/theme/csscache",array("theme" => $this->xmlattributes["Name"])) . "/" . $newname;
			}
			else {
				// then we need to link it to the css aggregator lookup page
				$link = WaxConf::LookupPath("web/") . "/util/css.php?" . $newname;
			}
			
			return $link;
		}
		function GetStylesheetPaths($forWeb = true) {
			$styles = $this->rGetResources("/\.css$/", WaxConf::LookupPath("fs/theme/cssdir",array("theme" => $this->xmlattributes['Name'])),$forWeb);
			return $styles;
		}
		function GetStylesheets() {
			$ret = array();
			foreach ($this->GetStylesheetPaths() as $sheet) {
				$ret[] = new Stylesheet($sheet);
			}
			return $ret;
		}
		
		function GetResources() {
			$elems = $this->GetScripts();
			
			$css = $this->GetAggregatedStylesheets();
			if ($css != NULL) {
				$elems[] = new Stylesheet($css);
			}
			
			return $elems;
		}
		
		// Override the RenderChildren function
		function RenderChildren(DOMDocument $doc) {
			$elems = array();
			
			foreach ($this->GetResources() as $elem) {
				$elems[] = $elem->Render($doc);
			}
			return $elems;
		}
	}
?>
