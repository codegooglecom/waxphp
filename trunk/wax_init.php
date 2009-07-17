<?
    ////////////////////////////////////////////////////
    // Wax
    //
    // Copyright 2008-2009 (c) Joe Chrzanowski
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
        
        private static $_debug = false;
        private static $_init = false;
        private static $_registered_tags = array();
        
        // Naming for the paths:
        // [varname]        another variable from the $_paths array
        // {varname}        a dynamic variable, passed to ParsePath
        // <varname>        a $_SERVER variable
        
        // DON'T CHANGE THESE UNLESS YOU KNOW WHAT YOU'RE DOING
        // You most likely should never ever have to touch this.
        private static $_paths = array(
            'relpath' 		=> '/wax',										// basically the web access url for Wax
            
            'web'			=> '[relpath]',
            
            'DOCUMENT_ROOT' => '<DOCUMENT_ROOT>',
            'fs' 			=> '<DOCUMENT_ROOT>[relpath]',
            'app'			=> '',
            
            'core' 			=> 'core',                                          // Folder that holds the core Wax functionality
            
            // where to locate a block
            'block'			=> '{block}.wax',
            
            // specify paths to different types of data
            'imagedir' 	 	=> 'images',
            'scriptdir'		=> 'js',
            'cssdir' 		=> 'css',
            'viewdir' 		=> 'views',
            'roledir'		=> 'roles',
            'csscache'  	=> 'csscache',
            
            // specify naming for each of the parts -- note images need an extension specified
            'image' 		=> '[imagedir]/{image}',
            'script'		=> '[scriptdir]/{script}.js',
            'css'			=> '[cssdir]/{css}.css',
            'role'			=> '[roledir]/{role}.php',
            'view'			=> '[viewdir]/{view}.view.php'
            
        );

        function __construct() { 
        	throw new Exception("ERROR: You can't instantiate a WaxConf object"); 
        }
        
        static function WaxRoot() {
        	return self::LookupPath("fs/");
        }

        // get a full path -- this function is responsible for any routing requests
        // as it looks up paths for the entire wax framework.
        static function LookupPath($what, $args = null) {
			$replaced = preg_replace("/([\w]+)/","[$0]",$what);
			
			$replaced = self::ResolvePaths($replaced);
			if (!is_null($args)) {
				$replaced = self::ResolveArgs($replaced,$args);
			}

			return $replaced;
        }
        
        // make sure the paths are only getting parsed once per page load
        // we can probably cache this somewhere too
        static function PreParse() {
            foreach (self::$_paths as $path => $parse) {
                self::$_paths[$path] = self::ResolvePaths($parse);
            }
        }
        
        // a couple functions to switch between filesystem and web paths
        static function FStoWEB($path) {
        	$path = str_replace(self::LookupPath("fs"),self::LookupPath("web/"),$path);
        	return $path;
        }
        static function WEBtoFS($path) {
        	$path = self::LookupPath("DOCUMENT_ROOT") . $path;
        	return $path;
        }
        
        // resolve paths using other variabels from the $_paths array
        // also replace any <VAR> vars with $_SERVER[VAR]
        static function ResolvePaths($path) {
            $matches = array();
            
            // replace the paths with whatever
            while (preg_match_all('/\[(\w+)]/',$path, $matches)) {
                foreach ($matches[1] as $match) {
                	if (isset(self::$_paths[$match]))
	                    $path = str_replace("[$match]",self::$_paths[$match],$path);
	                else {
	                	echo "Error - Invalid variable: $match<br />";
	                	$path = str_replace("[$match]",'',$path);
	                }
                }
            }
           
			// replace <var> with definitions from $_SERVER
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
        
        // register a tag class for use in template files
        static function RegisterTagClass($tagName, $className = NULL) {
        	self::$_registered_tags[$tagName] = ($className == NULL ? $tagName : $className);
        }
        static function GetTagClassName($tagName) {
        	if (self::TagLoaded($tagName)) {
        		return self::$_registered_tags[$tagName];
        	}
        	else return false;
        }
        static function TagLoaded($tagName) {
        	return isset(self::$_registered_tags[$tagName]);
        }
        
        // need to evaluate the block directory to get all the resources
        static function LoadBlock($block) {
        	$path = self::LookupPath("fs/app/block",array("block" => $block));
        	if (is_dir($path)) {
        		$block = new WaxBlock($path);
        	}
        	return $block;
        }
        static function GetBlockContext($file) {
        	$path = pathinfo($file);
        	$path = pathinfo($path['dirname']);
        	return $path['filename'];
        }
        
        private static function require_dir($dir) {
        	if (is_dir($dir)) {
        		$objects = scandir($dir);
	        	foreach ($objects as $file) {
	                if ($file[0] == ".") continue;
	                else if (is_dir("$dir/$file")) {
	                	self::require_dir("$dir/$file");
	                }
	                else {
	                	require_once("$dir/$file");
	                }
	            }
	        }
        }
        
        // initialize Wax
        static function Init($dir) {
            if (!self::$_init) {
            	// parse the conf array... need a way to hash/cache this
                self::PreParse();
                
                $dir = str_replace('\\','/',$dir);
                self::$_paths['app'] = str_replace(self::LookupPath('fs/'),'',$dir);
                
                $dir = self::LookupPath('fs/core');
				if (is_dir($dir))
	                self::require_dir("$dir");
	                
                self::$_init = true;
            }
        }
    }
    
    error_reporting(E_ALL);
    WaxConf::Init(getcwd());
?>