<?php
	abstract class Page {
		private $_page = null;	
		
		var $get;
		var $post;
		var $files;
		
		private $_important = array();
		
		/**
		* The page constructor.  This takes 2 arguments, one that
		* defines a default template to build the page - this is
		* in a sense the 'view' part, as it loads any interfaces
		* for certain actions
		* 
		* @param string $fromTemplate The view to use for this page
		* @param array $filters An array of Filter objects that can perform operations on superglobal arrays before using
		*/
		function __construct($fromTemplate = null, $filters = null) {
			// run thru any filters
			if ($filters)
				$this->AnalyzeFilters($filters);
				
			$this->get = $_GET;
			$this->post = $_POST;
			
			if ($_FILES) {
				$this->_files = $_FILES;
			}
		
			if ($fromTemplate) {
				$this->_page = $this->LoadTemplate($fromTemplate);
			}
			
			foreach (Wax::GetStylesheetsAndScripts() as $type => $arr) {	
				foreach ($arr as $obj) {
					if ($type == "js") {
						$this->AddScript($obj);
					}
					else if ($type == "css") {
						$this->AddStylesheet($obj);		
					}
				}
			}
		}
		
		/**
		* For creating links to pages
		* This function allows you to specify paramteters to the page
		* and it will generate a link to it.
		*
		* @param array $params The parameters to pass to the Linker
		*/
		function LinkTo($params) {
			// figure out the page access path
		}
		
		/**
		* Analyzes filter actions.  Filter actions generally manipulate
		* the GET, POST, or other raw data coming in.  You would usually be 
		* using this to modify POST or GET data into data chunks that could
		* be used by other objects during their construction/rendering
		*
		* @param array $filters The array of filters to process
		*/
		function AnalyzeFilters(array $filters) {
			foreach ($filters as $filter) {
				if ($filter instanceof Filter) 
					$filter->Execute($this);
				else {
					throw new Exception("Tried to pass invalid object as filter in: <pre>" . print_r($filters,true) . "</pre>");
				}
			}
		}
		
		/**
		* Add a stylesheet
		*
		* @param string $cssPath The stylesheet to include
		*/
		function AddStylesheet($cssPath) {
			$cur = new WaxWebControl("link");
			$cur->htmlattributes['rel'] = "stylesheet";
			$cur->htmlattributes['type'] = "text/css";
			$cur->htmlattributes['href'] = $cssPath;
			
			if (isset($this->_important['head']))
				$this->_important['head']->AddChild($cur);
		}
		
		/**
		* Add a stylesheet
		*
		* @param string $jsPath The script to include
		*/
		function AddScript($jsPath) {
			$cur = new WaxWebControl("script");
			$cur->htmlattributes['type'] = "text/javascript";
			$cur->htmlattributes['src'] = $jsPath;
			
			if (isset($this->_important['head']))
				$this->_important['head']->AddChild($cur);
		}

		
		/**
		* Applies a theme to the current site.  Basically it's a flag that
		* tells the page to include all of the scripts and stylesheets from
		* that particular theme
		*
		* @param string $theme The theme to import
		*/
		function ApplyTheme($theme) {
			$css = ThemeManager::GetStylesheetsFor($theme);
			$js = ThemeManager::GetScriptsFor($theme);
			
			foreach ($css as $stylesheet) {
				$this->AddStylesheet($stylesheet);
			}
			
			foreach ($js as $script) {
				$this->AddScript($script);
			}
		}
		
		/** 
		* A function that allows you to specify portions of the xml template file that should be easily accessible
		* from php.
		* 
		* @param string $placeholderID The ID of the control 
		* @param HTMLControl $control The control to mark as important
		*/
		function RegisterImportant($placeholderID, &$control) {
			if ($placeholderID)
				$this->_important[$placeholderID] = $control;
		}
		
		/**
		* Looks up an important control
		*
		* @param string $controlID The ID of the important control to look for 
		* @return mixed The HTMLControl asked for if found, null otherwise
		*/
		function LookupControl($controlID) {
			if ($this->_important[$controlID])
				return $this->_important[$controlID];
			else
				return NULL;
		}
		
		/**
		* Get all of the posted variables from a form
		* variables are in the form of formID_childID
		*
		* @param string $formID The ID of the form to get
		* @param string $method = 'REQUEST' Whether to get values from the POST, GET, or REQUEST arrays
		*/
		function GetForm($formID, $method = "REQUEST") {
			$method = $_REQUEST;
			if ($method == "GET") $method = $_GET;
			else if ($method == "POST") $method = $_POST;
			
			$ret = array();
			foreach ($method as $key => $val) {
				if (preg_match("/^($formID\_)/",$key)) {
					$key = preg_replace("/^($formID\_)/","",$key);
					$ret[$key] = $val;
				}
			}
			return $ret;
		}
		
		/**
		* The xml parsing function.  This function recursively goes 
		* through an xml document and converts it into a tree
		* of XMLControl objects.  
		* 
		* @param XMLReader $reader The XMLReader object to run through - must be initialized through Page::LoadTemplate
		*/
		private static function xmlLoadTemplate(XMLReader $reader) {
			$all = array();
            
            while ($reader->read()) {
                $node = null;
                $type = "html";
                $customname = "";
                switch ($reader->nodeType) {
                    case XMLReader::ELEMENT:
                    	// decide if it's a specialized class or a generic one
                    	// basically - if the class isn't define it will be rendered as an HTML control
                    	if (class_exists($reader->name)) {	
                    		$classname = $reader->name;
                    		$node = new $classname();
                    		$type = "custom";
                    	}
                    	else if (ValidHTMLControls::Contains($reader->name)) {
							$node = new WaxWebControl($reader->name);
                    	}
                    	else throw new Exception("Unknown Tag: {$reader->name}");
                        
                        if ($node) {
                            // if there's a child node or some text, recurse
                            if (!$reader->isEmptyElement) {
                                $res = self::xmlLoadTemplate($reader);
                                
                                if (!is_array($res))
                                    $node->value = $res;
                                else {
                                    foreach ($res as &$obj) {
                                        $node->AddChild($obj);
                                    }
                                }
                            }
	                      
                        	// grab all the attributes it specifies
                            $var = 0;
                            while ($reader->moveToAttributeNo($var++)) {
                                if ($type == "custom") {
                                    $node->xmlattributes[$reader->name] = $reader->value;
                                }
                                else
                                    $node->htmlattributes[$reader->name] = $reader->value;
                            }
                            
                            
                            // this is why this method can't be called from a static context --
                            // we need to maintain a heirarchy for proper functioning
                            if (method_exists($node,"OnConstruct")) {
                                $node->OnConstruct();
                            }
                            
                            $all[] = $node;
                        }
                    break;
                    
                    // return what's been parsed so far
                    case XMLReader::END_ELEMENT:
                        return $all;
                    break;
                    
                    case XMLReader::TEXT:
                    case XMLReader::CDATA:
                        $node = new WaxTextNode($reader->value);
                        $all[] = $node;
                    break;
                    
                    default: break;
                }
            }
            
            return $all;
        }
		
		/**
		* Wrapper function for xmlLoadTemplate.  Pass the url of an xml
		* template file.  
		*
		* @param string $templateFile The url of the template file to parse
		* @param bool $returnall If the file happens to be improperly formatted, defines whether or not to return all elements at level 0 or just the initial one
		*/
		static function LoadTemplate($templateFile, $returnall = false) {
			$reader = new XMLReader();
            if (file_exists($templateFile)) {
                $reader->open($templateFile);
                
                $res = self::xmlLoadTemplate($reader);
                return ($returnall ? $res : $res[0]);
            }
            else {
                throw new Exception("Couldn't load template '$templateFile': File not found.");
            }
		}
		
		/**
		* Binds an HTMLControl to a TemplatePlaceholder
		*
		* @param string $placeholderID The ID of the TemplatePlaceholder to bind this control to.
		* @param HTMLControl $control The HTMLControl to put into the TemplatePlaceholder.
		*/
		function LoadInto($placeholderID, $control) {
			if ($this->_important[$placeholderID]) {
				$this->_important[$placeholderID]->AddChild($control);
			}
		}
		
		function Render() {
			if ($this->_page) {
				$handled = false;
				if (isset($this->get['action'])) {
					if ($_POST && method_exists($this,$this->get['action'] . "_post")) {
						$actionhandler = $this->get['action'] . "_post";
						$this->$actionhandler();
						$handled = true;
					}
					else if (method_exists($this,$this->get['action'])) {
						$actionhandler = $this->get['action'];
						$this->$actionhandler();
						$handled = true;
					}
				}
				else { // if (!$this->get['action'] || !$handled) {
					if (method_exists($this,"index")) {
						$this->index();
					}
				}
				
				$domdoc = new DOMDocument('1.0','UTF-8');
				
				$result = $this->_page->Render($domdoc);
				$domdoc->appendChild($result);
				echo $domdoc->saveHTML();
			}
		}
	}
?>