<?
    // HTMLControl will Extend XMLControl
    class XMLControl {    
        // basic XML information
        var $attributes = null;
        var $value = "";
        var $tagname;
        var $parentnode = null;
        
        // a little more information
        protected $_force_render_value = false;
        protected $_allow = array();                // list of xmlattributes to allow --> 
        private $_haschildren = false;
        protected $_children = array();
        
        function __construct($tag,$value = null,$attributes = null) {
            $this->tagname = $tag;
            
            $this->attributes = new XMLAttributes($attributes);
            $this->xmlattributes =& $this->attributes;
            if ($value) $this->value .= $value;
        }

        private function insertChild(XMLControl $control, $index = null) {
            $control->parentnode = $this;
            if ($index !== null) {
                // shift nodes right to make room for the $control
                for ($x = count($this->_children); $x > $index; $x--) {
                    $this->_children[$x] = $this->_children[$x-1];
                }
                $this->_children[$index] =& $control;
            }
            else $this->_children[] =& $control;
        }
        function AddChild(XMLControl $control) {
            $this->insertChild($control);
        }
        function AppendChild(XMLControl $control) {
            $this->insertChild($control);
        }
        function PrependChild(XMLControl $control) {
            $this->insertChild($control,0);
        }
        
        function HasChildren() {
            return $this->CountChildren() > 0;
        }
        
        function FindChild($name) {
            foreach ($this->_children as $child) {
                if ($child->xmlattributes->ID == $name) {
                    return $child;
                }
            }
            return null;
        }
        function GetChildren() { return $this->_children; }
        function HasChild($name) {
            if ($this->FindChild($name) != null) return true;
            else return false;
        }
        function CountChildren() {
            return count($this->_children);
        }
        function ClearChildren() {
            $this->_children = array();
        }
        function GetChildrenOfTag($tag) {
            $return = array();
            foreach ($this->GetChildren() as $child) {
                if ($child->tagname == $tag) $return[] = $child;
            }
            return $return;
        }
        
        // functions for setting/getting properties
        // if allow is unspecified, assume the properties will be HTML attributes
        // if allow IS specified, assume the properties will NOT be HTML attributes
        //
        // this allows classes by default to act as html elements,
        // but when properties are specified, a custom tag is involved.
        function ValidAttributes() {
            // return which properties to allow
            return $this->_allow;
        }
        private function propertyAllowed($var) {
            return (
                    // see if we're restricting variables
                    (count($this->_allow) > 0 && 
                    (
                        // conditions to allow
                        array_key_exists($var,$this->_allow) || 
                        array_key_exists($var,$this->_passthru)
                    )) ||
                    count($this->_allow) >= 0
                );
        }
        function PropertyGet($var) {
            if (!$this->propertyAllowed($var))
                throw new PropertyNotFoundException($var);
            else
                return $this->xmlattributes->Get($var);
        }
        function PropertySet($var,$val) {
            if (!$this->propertyAllowed($var))
                throw new PropertyNotFoundException($var);
            else
                return $this->xmlattributes->Set($var,$val);
        }
        
        function __toString() { 
            $ret = $this->Render(true); 
            return ($ret ? $ret : "");
        }
        
        // perform any last minute object manipulation before rendering
        function Render($return = false) {
            $autoclose = false;
            if ((strlen($this->value) == 0 && !$this->HasChildren()) && !$this->_force_render_value) $autoclose = true;
            
            $buf = "<{$this->tagname}" . ($this->xmlattributes->Count() > 0 ? " " . $this->xmlattributes->__toString() : "") . ($autoclose ? " /" : "") . ">\n";
            
            if ($this->HasChildren()) {
                foreach ($this->_children as $child) {
                    $buf .= $child->Render(true);
                }
            }
            
            if ((!$this->HasChildren() && $this->value) || $this->_force_render_value) $buf .= $this->value;
            
            $buf .= ($autoclose ? "" : "\n</{$this->tagname}>\n");
            
            if ($return) return $buf; 
            else echo $buf;
        }
    }
?>