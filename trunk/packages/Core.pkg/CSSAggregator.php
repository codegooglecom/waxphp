<?php
	require_once("WaxControl.php");
	require_once("Theme.php");
	
	class CSSAggregator {
		private $_sources = array();
		private $_replacement_map = array();
		private $_theme = NULL;
		private $_tmpfile = '';
		private $_hashstring = '';
		
		function __construct(Theme $theme) {
			$this->_theme = $theme;
			$this->_replacement_map = $this->_theme->GetCSSVars();
			$this->_sources = $theme->GetStylesheetPaths(false);
			
			$this->importFromSources();
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
					$contents = str_replace("[$search]", $this->lookupReplacement($search), $contents);
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
			}
			
			$this->go($fh);
		}
	}
?>
