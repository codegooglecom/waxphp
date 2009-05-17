<?
    abstract class RenderableAttributes {
        abstract function __toString();
        
        function Render($return = false) {
            if ($return) return $this->__toString();
            else echo $this->__toString();
        }
    }
    
    class StyleAttributes extends Attributes {
        function __toString() {
            $parts = array();
            if (count($this->_vals)) {
                foreach ($this->_vals as $var => $val) {
                    $parts[] = "$var:$val";
                }
                return implode(";",$parts);
            }
            else return "";
        }
    }
    
    class XMLAttributes extends Attributes {
        function __set($var,$val) {
            $this->Set($var,$val);
        }
        function __toString() {
            $buf = array();
            if (count($this->_vals)) {
                foreach ($this->_vals as $attrib => $value) {
                    if (strlen(trim($value)) == 0) continue;
                    
                    $buf[] = "$attrib='$value'";
                }
                return implode(" ",$buf);
            }
            else return "";
        }
    }
?>