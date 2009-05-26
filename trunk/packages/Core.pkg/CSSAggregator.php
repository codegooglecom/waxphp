<?php
	require_once("WaxControl.php");
	require_once("Theme.php");
	
	class CSSAggregator {
		private $_sources = array();
		private $_replacement_map = array();
		private $_theme = NULL;
		private $_tmpfile = '';
		private $_hashstring = '';
		
		function __construct(Theme $theme, $stylesheets = NULL) {
			$this->_theme = $theme;
			$this->_replacement_map = $this->_theme->GetCSSVars();
			if (is_array($stylesheets))
				$this->_sources = $stylesheets;
			else
				$this->_sources = $theme->GetStylesheetPaths(false);
			
			$this->importFromSources();
		}
		
		/**
		* This function returns the location of the file that has
		* aggregated all of the theme's CSS files and applied the 
		* necessary variables to them.
		*/
		function GetAggregatedStylesheets() {
			$csscache = WaxConf::LookupPath("fs/theme/csscache",array("theme" => $this->_theme->xmlattributes["Name"]));
			$newname = basename($this->GetTmpName());
			$link = NULL;
			
			// if the cache directory doesn't exist, create it
			if (!is_dir($csscache)) {
				mkdir($csscache, 0600);
			}
			
			// if we have a cache directory, try saving the file there for direct reference
			if (is_dir($csscache)) {
				if (!is_file("$csscache/$newname"))
					rename($this->GetTmpName(), $csscache . "/" . $newname);
					
				$link = WaxConf::LookupPath("web/theme/csscache",array("theme" => $this->_theme->xmlattributes["Name"])) . "/" . $newname;
			}
			else {
				// then we need to link it to the css aggregator lookup page
				$link = WaxConf::LookupPath("web/") . "/util/css.php?" . $newname;
			}
			
			return $link;
		}
		
		function getHash() {
			return md5($this->_hashstring);
		}
		
		function GetTmpName() {
			return $this->_tmpfile;
		}
				
		private function lookupReplacement($var) {
			if (isset($this->_replacement_map[$var])) {
				return $this->_replacement_map[$var];
			}
			else return '';
		}
		
		
		private function go($fh) {
			$contents = "";
			
			$start = microtime(true);
			$this->_tmpfile = sys_get_temp_dir() . "/" . $this->getHash() . ".css";
			
			if (!is_file($this->_tmpfile)) {
				fseek($fh,0);
				while (!feof($fh)) {
					$contents .= fread($fh,1024);
				}
				
				$lookfor = array();
				preg_match_all("/\[(\w+)\]/",$contents,$lookfor);
				foreach ($lookfor[1] as $search) {
					$replace = $this->lookupReplacement($search);
					$contents = str_replace("[$search]", $replace, $contents);
				}
				
				file_put_contents($this->_tmpfile, $contents);
			}
		}
				
		private function importFromSources() {
			// need to load a Theme object here
			$fh = tmpfile();
			if (!$fh) return false;
			
			foreach ($this->_sources as $file) {
				$filecontents = file_get_contents($file);
				$this->_hashstring .= md5($filecontents);
				
				fwrite($fh, $filecontents);
				fwrite($fh, "\r\n");
			}
			
			$this->go($fh);
		}
	}
?>
