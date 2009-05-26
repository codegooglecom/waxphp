<?
	require_once("WaxWebControl.php");
	
    class TemplatePlaceholder extends WaxWebControl {
        function __construct() {
            parent::__construct("div");
            $this->_allow = array("Src");
        }
        
        function OnConstruct() {
            if (!isset($this->xmlattributes['Src'])) {
                // then return the empty div
            }
            else {  
                if (file_exists($this->xmlattributes['Src'])) {
                    $result = Page::LoadTemplate($this->xmlattributes['Src']);
                    $this->AddChild($result);
                }
                else {
                    $rt = new RawText();
                    $rt->xmlattributes['Text'] = "ERROR: Couldn't find template: " . $this->xmlattributes['Src'] . "<br>";
                    $this->AddChild($rt);
                }
            }
        }
    }
?>