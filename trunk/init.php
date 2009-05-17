<?
    ////////////////////////////////////////////////////
    // Wax
    // Web development with Integrated Scripts and PHP
    //
    // Copyright 2008 (c) Joe Chrzanowski
    ////////////////////////////////////////////////////
    
    // Configurations
    
    // Be extremely careful with WaxConf objects.
    // Any paths that must be determined go through the WaxConf object
    // Therefore, you can modify the entire framework structure by 
    // modifying the path variables within the WaxConf object.
    
    // If you mess this up you might have a tough time fixing it.
    class WaxConf {
        const FS = 'fspath';
        const WEB = 'webpath';
        const OTHER = 'nullpath';
        
        private static $_title = 'Default';
        private static $_debug = false;
        private static $_init = false;
        private static $_loadedobjects = array();
        
        // Naming for the paths:
        // [varname]        another variable from the $_paths array
        // {varname}        a dynamic variable, passed to ParsePath
        // <varname>        a $_SERVER variable
        
        
        // DON'T FUCK WITH THIS UNLESS YOU KNOW WHAT YOU'RE DOING
        // You most likely should never ever have to touch this.
        private static $_paths = array(
            'relpath' 	=> '/Wax',										// basically the web access url for Wax
            'web' 		=> '[relpath]',
            'fs' 		=> '<DOCUMENT_ROOT>[relpath]',
            'nullpath' 	=> '',
            'core' 		=> 'core',                                          // Folder that holds the core Wax functionality
            
            // specify paths to different types of data
            
            'theme' 	=> 'themes/{theme}',
            'package'	=> 'packages/{package}.pkg',
            
            'imagedir'  => 'img',
            'scriptdir'	=> 'js',
            'cssdir' 	=> 'css',
            'layoutdir' => 'layouts',
            
            'image' 	=> '[imagedir]/{image}',
            'script'	=> '[scriptdir]/{script}.js',
            'css'		=> '[cssdir]/{css}.css',
            'layout'	=> '[layoutdir]/{layout}.xml'
        );
        private static $_options = array(
            'debug' => false,
            'handle_exceptions' => true,
        );
        
        function __construct() { throw new Exception("ERROR: You can't instantiate a WaxConf object"); }
        
        static function PackageExists($package) {
            $path = self::LookupPath('fs/package',array('package' => $package));
            if (is_dir($path)) return true;
            else return false;
        }
        static function ThemeExists($theme) {
            $path = self::LookupPath('fs/theme',array('theme' => $theme));
            if (is_dir($path)) return true;
            else return false;
        }
        
        // get a directory
        static function GetDirPath($type, $path, $vars = null) {
        	
        }
        
        static function GetPath($type, $path, $vars = null) {
            if (self::$_paths[$path]) {
                $parsedpath = self::ResolvePaths("[$type]") . '/' . self::ResolvePaths(self::$_paths[$path]);
                $parsedpath = self::ResolveArgs($parsedpath,$vars);
                
                // make it look right
                while (strpos($parsedpath,'//') !== false)
                    $parsedpath = preg_replace("/\/+/","/",$parsedpath);  
                
                return $parsedpath;
            }
            else return '';
        }

        // get a full path
        static function LookupPath($what, $args = null) {
            // pass something that aggregates the path vars
			// first replace all occurrences of strings with their proper counterparts
			$what = preg_replace("/([\w]+)/","[$0]",$what);
			$what = self::ResolvePaths($what);
			if ($args) {
				$what = self::ResolveArgs($what,$args);
			}
			return $what;
        }
        
        
        static function PushObject($objname) {
            self::$_loadedobjects[] = $objname;
        }
        
        // make sure the paths are only getting parsed once per page load
        static function PreParse() {
            foreach (self::$_paths as $path => $parse) {
                self::$_paths[$path] = self::ResolvePaths($parse);
            }
        }
        
        // resolve paths using other variabels from the $_paths array
        // also replace any <VAR> vars with $_SERVER[VAR]
        static function ResolvePaths($path) {
            $matches = array();
            
            while (preg_match_all('/\[(\w+)]/',$path, $matches)) {
                foreach ($matches[1] as $match) {
                    $path = str_replace("[$match]",self::$_paths[$match],$path);
                }
            }
            preg_match_all('/<(\w+)>/',$path, $matches);
            foreach ($matches[1] as $match) {
                $path = str_replace("<$match>",$_SERVER[$match],$path);
            }
            
            return $path;
        }
        
        // get a path using specific arguments
        // example:
        //      ResolveArgs('image',array('package'=>'Alerts/Message', 'image' => 'error'));
        // returns the path to the error.png image relative to the Wax root folder
        static function ResolveArgs($path,$vars) {
            $matches = array();
            
            if (!is_array($vars)) return $path;
            
            foreach ($vars as $key => $val) {
                $path = str_replace("{" . $key . "}",$val,$path);
            }
            return $path;
        }
        
        // initialize Wax
        static function Init() {
            if (!self::$_init) {
            	// parse the conf array... need a way to hash/cache this
                self::PreParse();
                
                $dir = self::LookupPath('fs/core');
                $objects = scandir($dir);
                foreach ($objects as $file) {
                    if ($file[0] == "." || is_dir("$dir/$file")) {
                    	continue;
                    }
                    else {
                        require_once("$dir/$file");
                    }
                }
                self::$_init = true;
            }
        }
        static function GetOption($opt) { return self::$_options[$opt]; }
    }
    
    error_reporting(E_ALL);
    
    // initialize Wax
    WaxConf::Init();
    
    // Import basic packages
    Wax::Import("Core");    
    Wax::Import("HTML/Standard");
?>