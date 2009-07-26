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
		
		function __construct($blockpath, $include_files = false) {
			$info = pathinfo($blockpath);
			$this->name = $info['filename'];
			$this->loadResources($blockpath, $include_files);
		}
				
		private function loadResources($dir, $include_files = false) {
			$allowed = array("roles","views","js","css","images");
			foreach ($allowed as $file) {	
				if (is_dir("$dir/$file")) {
					foreach (scandir("$dir/$file") as $thisfile) {
						if ($thisfile[0] == '.') continue;
						else {
							if ($file == "roles" && $include_files) {
								require_once("$dir/$file/$thisfile");
							}
							if ($file == "views")
								$this->_resources[$file][array_shift(explode(".",$thisfile))] = "$dir/$file/$thisfile";
							else {
								$path = Wax::LookupPath("web/block/$file",array("block" => $this->name, $file => array_shift(explode(".",$thisfile))));
								if (!file_exists($path))
									$path = Wax::FStoWEB("$dir/$file/$thisfile");
								$this->_resources[$file][array_shift(explode(".",$thisfile))] = $path;
							}
						}
					}
				}
			}
			foreach (scandir($dir) as $file) {
				if ($file[0] == '.') continue;			// the file is hidden
				else if ($file[0] == "_") continue;		// the file is disabled
				else if (is_dir("$dir/$file")) continue;
				else {
					$ext = array_pop(explode('.',$file));
					switch ($ext) {
						case "php":
							if ($include_files) {
								require_once($dir . '/' .$file);
							}
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