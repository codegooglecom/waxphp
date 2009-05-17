<?
    /***********************************************************
     * HTMLControl : Inherits XMLControl
     * Used for rendering objects
     * --------------------------------------------------------- 
     * (c) 2007, 2008 Joe Chrzanowski
     * ---------------------------------------------------------
     * This class controls the management of the element's 
     * starting and ending HTML tags as well as all attributes
     *
     * Essentially a class for creating custom HTML controls
     ***********************************************************/
     
    require_once("Attributes.php");
    require_once("XMLControl.php");
    require_once("ValidHTMLControls.inc.php");
    
    class HTMLTagNotFoundException extends Exception {}
    
    class HTMLControl extends XMLControl {
        var $style = null;                  // All 'style' properties
        var $htmlattributes = null;
        var $xmlattributes = null;
        protected $_vars = array();
        protected $_passthru = array();             // array that provides simple passthru attribute functionality
        protected $_preserve_attributes = false;
        
        function __construct($tag, $innerhtml = null, $attributes = null, $style = null) {
            parent::__construct($tag,$innerhtml,$attributes);
            
            if (!ValidHTMLControls::Contains($tag)) {
                throw new HTMLTagNotFoundException($tag);
                return; 
            }
            
            $this->htmlattributes = new XMLAttributes();
            $this->xmlattributes = $this->attributes;
            
            // support certain passthru's
            $this->_passthru["id"] = "id";
            $this->_passthru["ID"] = "id";
            $this->_passthru["ClassName"] = "class";
            
            $this->_force_render_value = ValidHTMLControls::ForceRenderValue($tag);
            $this->style = new StyleAttributes($style);
        }
        
        function SetHTML($value) {
            $this->value = $value;
        }
        function GetHTML() {
            return $this->value;
        }
        
        function Render($return = false) {
            // handle passthru variables
            foreach ($this->_passthru as $var => $htmlattr) {
                if ($this->xmlattributes->GetNonCS($var)) {
                    $this->htmlattributes->Set($htmlattr,$this->xmlattributes->GetNonCS($var));
                }
            }
            
            // call the build function to put the object together
            if (method_exists($this,"Build"))
                $this->Build();
            
            // set the styles
            if (!$this->htmlattributes->style && is_object($this->style)) {
                $this->htmlattributes->style = $this->style->__toString();
            }
                
            $this->xmlattributes = $this->htmlattributes;
            
            return parent::Render($return);
        }
        
        function __toString() {
            echo "<b>WARNING: </b> using the __toString() method of HTMLControls is deprecated.  Use ->Render(true) instead<br><pre>";
            debug_print_backtrace();
            echo "</pre>";
            return "";
        }

        function SetAttribute($var,$val) { $this->htmlattributes->Set($var,$val); }
        function GetAttribute($var) { return $this->htmlattributes->Get($var); }
        function SetStyle($var,$val) { $this->style->Set($var,$val); }
        function GetStyle($var) { return $this->style->$var; }
    }
?>