<?
    // WISP Image Class
    // This class interfaces with the WISP kernel to fetch images
    
    class Image extends WaxWebControl {
        function __construct() {
            // specify that this is an image tag
            parent::__construct("img");
            
            // which properties to allow
            $this->_allow = array("From","Which","Src");
        }
        function OnConstruct() {
            $actualsrc = "";
            
            if (!$this->xmlattributes["Src"]) echo "SOURCE UNSPECIFIED<br>";                //throw new MissingPropertyException("Src");
            
            if ($this->xmlattributes['From']) {
                if (!$this->xmlattributes["Which"]) echo "WHICH UNSPECIFIED<br />";    //throw new MissingPropertyException("Which");
                $actualsrc = WaxConf::LookupPath("web/" . $this->xmlattributes["From"] . "/image",array($this->xmlattributes["From"] => $this->xmlattributes["Which"], 'image' => $this->xmlattributes["Src"]));
            }
            else {
                $actualsrc = $this->xmlattributes["Src"];
            }
            
            $this->htmlattributes['style'] = "border:0px;";
            $this->htmlattributes['src'] = $actualsrc;
        }
    }
?>