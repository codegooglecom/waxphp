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
		public static $active = NULL;
		
		function OnConstruct() {
			if (isset($this->xmlattributes['Name'])) {
				$this->_themepath = WaxConf::LookupPath("fs/theme",array("theme" => $this->xmlattributes['Name']));
				$this->_themeconf = Page::LoadTemplate($this->_themepath . "/theme.waxml");
				self::$active = $this;
				
				$this->_cssaggregator = new CSSAggregator($this);
			}
		}
		
		function GetCSSVars() {
			return $this->_themeconf->GetCSSVars();
		}
		static function GetActiveTheme() {
			if (!is_null(self::$active)) {
				return self::$active;
			}
			else return NULL;
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
					if ($convertForWeb) {
						$file = WaxConf::FStoWEB($path . '/' . $file);
						$base[] = $file;
					}
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
		
		function GetAggregatedStylesheets() {
			return $this->_cssaggregator->GetAggregatedStylesheets();
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
