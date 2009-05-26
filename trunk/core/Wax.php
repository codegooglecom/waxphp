<?php
	class Wax {
		private static $_jscss = array('js' => array(), 'css' => array());
		/**
		* Add a library to the Wax runtime
		*
		* @param string $filename The file to include 
		*/
		static function PushObject($filename) {
			require_once($filename);
		}
		
		/**
		* Get the stylesheets and scripts that packages have loaded
		*/
		static function GetStylesheetsAndScripts() {
			return self::$_jscss;
		}
		
		static function XMLFixEntities($text) {
			// use a list of standard HTML entities and their corresponding ASCII character codes to allow their usage in WaxML documents
			$xtoh = array(
				"&nbsp;" => 160,
				"&lt;" => 60,
				"&gt;" => 62,				
				"&cent;" => 162,
				"&pound;" => 165,
				"&yen;" => 165,
				"&euro;" => 8364,
				"&sect;" => 167,
				"&copy;" => 169,
				"&reg;" => 174,
				
				"&amp;" => 38 		// have to do this last.
			);
			foreach ($xtoh as $key => $num) $xtoh[$key] = "&#$num;";	// plug in the numbers to &#____;
			return str_replace(array_keys($xtoh),array_values($xtoh),$text);
		}
		
		
		/**
    	* Import a Wax package into the Wax Runtime
    	* 
    	* @param string $package The package name to import
    	*/
        static function Import($package) {
            // make sure that the package is an actual path
            // then it should be a file
            $filename = (is_file($package) ? $package : WaxConf::LookupPath("fs/package",array("package" => $package)));
            
            $ret = array('css'=> array(), 'js' => array());
            
            if (!is_dir($filename)) throw new Exception("ERROR: Invalid Object '" . $package . "'");
            else {
                // need to include the php objects
                $files = scandir($filename);
                foreach ($files as $file) {
                    if ($file[0] == '.' || is_dir("$filename/$file")) continue;
                    Wax::PushObject($filename . "/$file");
                }
                
                $css = WaxConf::LookupPath("fs/package/cssdir",array('package' => $package));
                $webcss = WaxConf::LookupPath("web/package/cssdir",array('package' => $package));
                if (is_dir($css)) {
                	$cssfiles = scandir($css);
	                foreach ($cssfiles as $cssfile) {
	                	if ($cssfile[0] == '.') continue;
	                	$ret['css'][] = "$webcss/$cssfile";
	                }
	            }
                $js = WaxConf::LookupPath("fs/package/scriptdir",array('package' => $package));
                $webjs = WaxConf::LookupPath("web/package/scriptdir",array('package' => $package));
                if (is_dir($js)) {
                	$jsfiles = scandir($js);
	                foreach ($jsfiles as $jsfile) {
	                	if ($jsfile[0] == '.') continue;
	                	$ret['js'][] = "$webjs/$jsfile";
	                }
	            }
            }
            
            return $ret;
        }
        
        /**
    	* Get the filesystem path of a package
    	* 
    	* @param string $package The package name to get the path of
    	*/
        static function GetPackagePath($package) {
            $path = WaxConf::GetPath(WaxConf::FS,'package',array('package'=>$package));
            return $path;
        }
        
        /**
        * Find out if a package name is installed
        *
        * @param string $package The package name to check
        */
        static function PackageIsValid($package) {
            $path = self::GetPackagePath($package);
            if (is_dir($path)) return true;
            else return false;
        }
        
        /**
        * Get the path of an image from a package
        *
        * @param string $package The package to get the image from
        * @param string $image The image name to get the path of
        */
        static function GetImage($package,$image) {
            if (self::PackageIsValid($package)) {
                return WPM::GetFromPackage($package,'image',$image);
            }
        }
        
        /**
        * Get the path of a stylesheet from a package
        * 
        * @param string $package The package to get the stylesheet from
        * @param string $css The stylesheet to get
        */
        static function GetStylesheet($package,$css) {
            if (self::PackageIsValid($package)) {
                return WPM::GetFromPackage($package,'css',$css);
            }
        }
        
        /**
        * Get a script from a package
        * 
        * @param string $package The package to get the javascript from
        * @param string $js The script to get
        */
        static function GetScript($package,$js) {
            if (self::PackageIsValid($package)) {
                return WPM::GetFromPackage($package,'js',$js);
            }
        }
        
        /**
        * Get a layout from a package
        * 
        * @param string $package The package to get the layout from
        * @param string $layout The layout to get
        */
        static function GetLayout($package,$layout) {
            if (self::PackageIsValid($package)) {
                return FPM::GetFromPackage($package,'layout',$layout);
            }
        }
	}
?>