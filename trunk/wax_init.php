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
        private static $_debug = false;
        private static $_init = false;
        
        private static $_version = array(
        	"version"	=> 0,
        	"revision"  => 6,
        	"build" 	=> 1
        );
        
        // Naming for the paths:
        // [varname]        another variable from the $_paths array
        // {varname}        a dynamic variable, passed to ParsePath
        // <varname>        a $_SERVER variable
        
        // DON'T CHANGE THESE UNLESS YOU KNOW WHAT YOU'RE DOING
        // You most likely should never ever have to touch this.
        private static $_paths = array(
        	// important base paths needed to determine working directories, etc..
            'relpath' 		=> '/wax',										// basically the web access url for Wax
            'web'			=> '[relpath]',
            'DOCUMENT_ROOT' => '<DOCUMENT_ROOT>',
            'fs' 			=> '<DOCUMENT_ROOT>[relpath]',
            'app'			=> '',
            'core' 			=> 'core',                                          // Folder that holds the core Wax functionality
                        
            // specify folder names for different types of data
            'blockdir' 		=> 'blocks',
            'imagedir' 	 	=> 'images',
            'scriptdir'		=> 'js',
            'cssdir' 		=> 'css',
            'viewdir' 		=> 'views',
            'roledir'		=> 'roles',
            'csscache'  	=> 'csscache',
            
            // specify naming for each of the parts
            'appblock'		=> '{block}.wax',
            'block'			=> '[blockdir]/{block}.wax',
            'image' 		=> '[imagedir]/{image}',
            'script'		=> '[scriptdir]/{script}.js',
            'css'			=> '[cssdir]/{css}.css',
            'role'			=> '[roledir]/{role}.php',
            'view'			=> '[viewdir]/{view}.view.php',
            
            // naming shortcuts for blocks - these look kind of weird
            // the purpose is to allow for easier name resolution by using the 
            // folder name in the block as the variable instead of the singlular 
            // version of whatever we're looking for
            'roles'			=> '[roledir]/{roles}.php',
            'images'		=> '[imagedir]/{images}',
            'js' 			=> '[scriptdir]/{js}.js'
        );
        
        // paths to look for blocks
        private static $_blockpath = array(
        	"fs/app/appblock",	// /app/Block.wax
    		"fs/app/block",		// /app/blocks/Block.wax
    		"fs/block",			// /wax/blocks/Block.wax
        );
        
        // blocks to autoload (plugins/libraries/themes)
        private static $_autoload = array(
        	"mvc",		// allows for MVC based web development
        	"default"   // default HTML themes for wax
        );
        
        
        private static $_loaded_blocks = array();

        function __construct() { 
        	throw new Exception("ERROR: You can't instantiate a WaxConf object"); 
        }
        
        static function Version($as_number = true) {
        	$v = self::$_version;
        	return $v['version'] . "." . $v['revision'] . "." . $v['build'];
        }
        
        static function WaxRoot() {
        	return self::LookupPath("fs/");
        }

        // get a full path -- this function is responsible for any routing requests
        // -- it looks up paths for the entire wax framework, therefore,
        // the entire framework can be restructured by modifying only the path
        // variables above, then making the proper corresponding calls to this function
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
        
        // block functions
        static function LoadBlock($block) {
        	if (!isset(self::$_loaded_blocks[$block])) {
	        	$path = self::findBlock($block);
	        	
	        	$blockobj = new WaxBlock($path, true);
	        	self::$_loaded_blocks[$block] = $blockobj;
	        	
	        	return $blockobj;
	        }
	        else return self::GetBlock($block);
        }
        
        // returns a block by name -- looks thru the blockpath
        // to try and find the block
        static function GetBlock($block = __FILE__) {
        	if (is_null($block))
        		echo "Trying to get block in $block<br />";
        	
        	if (isset(self::$_loaded_blocks[$block]))
        		return self::$_loaded_blocks[$block];
        	else {
	        	$path = self::findBlock($block);
	        	if (is_null($path)) return NULL;
	        	else return new WaxBlock($path);
	        }
        }
        
        // used mostly for getting all resources needed for an application
        // usually in a header or something of the sort
        static function GetLoadedBlocks() {
        	return self::$_loaded_blocks;
        }
        // find out which block a file is located in -- doesn't always work
        static function GetBlockContext($file) {
        	$path = pathinfo($file);
        	$path = pathinfo($path['dirname']);
        	return $path['filename'];
        }
        
        
        // Private functions
        private static function findBlock($block) {
        	foreach (self::$_blockpath as $path) {
        		$blockloc = self::LookupPath($path,array("block" => $block));
	        	if (is_dir($blockloc)) {
	        		return $blockloc;
	        	}
	        }
        	return NULL;
        }
        // require the files in a directory
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
            	// make sure the paths have been preparsed (for performance)
                self::PreParse();
                
                // determine application
                $dir = str_replace('\\','/',$dir);
                self::$_paths['app'] = str_replace(self::LookupPath('fs/'),'',$dir);
                
                // require core
                $dir = self::LookupPath('fs/core');
				if (is_dir($dir))
	                self::require_dir("$dir");
	                
	            // autoload blocks
	            foreach (self::$_autoload as $block) {
	            	self::LoadBlock($block);
	            }
	            
	            // ready to go
                self::$_init = true;
            }
        }
    }
    
    error_reporting(E_ALL);
    WaxConf::Init(getcwd());
?>