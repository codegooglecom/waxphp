<?
    class CodePanel extends WaxWebControl {
    	var $keywords = array(
    		"class","array","interface",
    		"extends","abstract","private",
    		"public","static","protected",
    		"require_once","function",
    		"WISP",'$this','self'
    	);
    	private $_parsedcode = '';
    	// class has to go first
    
    	function __construct() {	
    		parent::__construct("div");
    		$this->htmlattributes['class'] = "codepanel";
    	}
    	function OnConstruct() {
    		if (isset($this->xmlattributes['SourceFile'])) {
    			$this->xmlattributes['Code'] = file_get_contents($this->xmlattributes['SourceFile']);
    		}
    	}
    	function PreRender() {
    		$code = htmlentities($this->xmlattributes['Code']);
    		
    		$result = array();
    		
    		$code = str_replace("'","&apos;",$code);
    		
    		// the standard rules - keywords and spacing
    		$code = str_replace(array("\n","\t","    "),array("<br />\n","&nbsp;&nbsp;&nbsp;&nbsp;","&nbsp;&nbsp;&nbsp;&nbsp;"),$code);
			foreach ($this->keywords as $keyword) {
				$code = str_replace($keyword,"<span class='keyword'>$keyword</span>",$code);
			}
			
			// php tags
			$code = str_replace('&lt;?php',"<span class='phptag'>&lt;?php</span>",$code);
			$code = str_replace('?&gt;',"<span class='phptag'>?&gt;</span>",$code);
			
			// strings & numbers
			$code = str_replace("&quot;",'"',$code);
			$code = preg_replace("/((\b[0-9]+)?\.)?[0-9]+\b/","<span class='number'>$0</span>",$code);
			$code = preg_replace('/\"([^\"]+)\"/',"<span class='string'>&quot;$1&quot;</span>",$code);
			
			// comments
			$code = preg_replace("/\/\/[^\n]+/","<span class='comment'>$0</span>",$code);
			$code = preg_replace("/\/\*(.|\r|\n)*?\*\//","<span class='comment'>$0</span>",$code);
			
			// php variables
			$code = preg_replace('/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/',"<span class='variable'>$0</span>",$code);
			
			// some more complex rules - functions and classnames
			$code = preg_replace("/new ([\w]+)\(\)/","<span class='object'>$1</span>()",$code);
			$code = preg_replace("/([\w]+)([\w]*)\(/","<span class='function'>$1</span>(",$code);
			
    		$this->_parsedcode = $code;
    	}
		function RenderChildren(DOMDocument $doc, DOMNode $ctrl) {
			$tmpdoc = $doc->createDocumentFragment();
			
			$this->_parsedcode = Wax::XMLFixEntities($this->_parsedcode);
			$tmpdoc->appendXML($this->_parsedcode);
			$ctrl->appendChild($tmpdoc);
			
			return $ctrl;
		}
    }
?>