<?php
    // This class is responsible for parsing the RSS feeds from concerto
    // The XML node class is used to build an object heirarchy which can be 
    // parsed and returned to the user.
    class XMLNode {
        var $tagname;
        var $attributes;
        var $chardata;
        var $children;
        
        function __construct($tagname,$attribs) {
            $this->tagname = $tagname;
            $this->attributes = $attribs;
        }
    }
    
    $xmldatastack = array();
    
    function xml_helpers_reinit() {
        global $xmldatastack;
        $xmldatastack = array();
    }
    function begin_tag_parse($xmlparser,$tag,$attribs) {
        global $xmldatastack;
        
        $obj = new XMLNode($tag,$attribs);
        $xmldatastack[] = $obj;
    }
    function end_tag_parse($xmlparser,$tag) {
        global $xmldatastack;
        
        $xmlnode = array_pop($xmldatastack);
        $parent = array_pop($xmldatastack);
        $parent->children[] = $xmlnode;
        $xmldatastack[] = $parent;
    }
    function char_data($xmlparser,$data) {
        global $xmldatastack;
        
        $obj = array_pop($xmldatastack);
        $obj->chardata = $data;
        $xmldatastack[] = $obj;
    }
    
    function get_parsed_xml_data() {
        global $xmldatastack;
        return $xmldatastack;
    }
?>