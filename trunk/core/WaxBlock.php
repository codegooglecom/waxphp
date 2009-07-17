<?php
	/**
	*  Base class for WaxBlocks -- can be used to build plugins/tags/etc.
	*  This class defines the public class for a WaxBlock, so any public functions
	*  should have a definition in a class that extends WaxBlock
	*/
	class WaxBlock {
		private $_resources = array(
			'views' => array(),
			'js' => array(),
			'css' => array(),
			'images' => array(),
			'roles' => array()
		);
		var $name = NULL;
		
		function __construct($blockpath) {
			$info = pathinfo($blockpath);
			$this->name = $info['filename'];
			$this->loadResources($blockpath);
		}
				
		private function loadResources($dir) {
			foreach (scandir($dir) as $file) {
				if ($file[0] == '.') continue;			// the file is hidden
				else if ($file[0] == "_") continue;		// the file is disabled
				else if (is_dir("$dir/$file")) {
					// then the directory is probably a special one:
					$allowed = array("views","js","css","images","roles");
					
					if (array_search($file,$allowed) !== false) {
						foreach (scandir("$dir/$file") as $thisfile) {
							if ($thisfile[0] == '.') continue;
							else {
								$this->_resources[$file][array_shift(explode(".",$thisfile))] = "$dir/$file/$thisfile";
							}
						}
					}
				}
				else {
					$ext = array_pop(explode('.',$file));
					switch ($ext) {
						case "php":
							// the file (should) contain a class or 2 -- include them into the runtime
							require_once($dir . '/' .$file);
						break;
						default:
							// don't know... php files should be the only ones here
							break;
					}
				}
			}
		}
		
		function GetResources() {
			return $this->_resources;
		}
		
		function __call($func, $args) {
			// redirect to the proper array
			if (isset($this->_resources[$func])) {
				$arg = $args[0];
				return (isset($this->_resources[$func][$arg]) ? $this->_resources[$func][$arg] : NULL);
			}
			else return NULL;
		}
		function __get($var) {
			// return the array
			if (isset($this->_resources[$var]))
				return $this->_resources[$var];
		}
	}
?>