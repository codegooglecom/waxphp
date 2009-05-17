<?php
    class ConcertoException extends Exception {}
    class ConcertoConnectionClassNotFoundException extends ConcertoException {}
    class ConcertoNotConnectedException extends ConcertoException {}
    class ConcertoConnectionFailedException extends ConcertoException {}
    class ConcertoXMLException extends ConcertoException {}
    
    require_once("ConcertoXMLHelpers.php");        // for parsing RSS and XML
    require_once("ConcertoHTTPConnection.php");
    require_once("ConcertoXMLFileConnection.php");
    
    class ConcertoFormats {
        const RSS = 'rss';
        const HTML = 'html';
        const RAW = 'raw';
        const RAW_HTML = 'rawhtml';
        const JSON = 'json';
    }
        
    class ConcertoClient {
        private $_server = null;
        private $_port = null;
        private $_apiv = null;
        private $_format = ConcertoFormats::RSS;
        
        private $_connection = null;
        
        var $Feeds;
        var $Types;
        
        // construct the client -- basically do nothing
        function __construct() {
        }
        
        // change the connection -- allows the client to connecto the main concerto server or any other pseudo-concerto interface
        // don't really need to sanitize this because it should be hard coded in the application that's using the client
        function OpenConnection($type,$args) {
            if (class_exists($type)) {
                $this->_connection = new $type($args);
                if ($this->_connection->Verify())
                    return true;
                else throw new ConcertoConnectionFailedException();
            }
            else throw new ConcertoConnectionClassNotFoundException();
        }   
        
        // parse the structured XML file into a useable php array
        private function parse_struct($params) {
            $xmldatastruct = get_parsed_xml_data();
            if ($params['select'] == "feed" || $params['select'] == "content")
                $parsed = $this->simplify_feed($xmldatastruct[0]->children[0]);
            else if ($params['select'] == "system")
                $parsed = $this->simplify_sysinfo($xmldatastruct[0]->children[0]);
            else
                $parsed = $this->simplify($xmldatastruct[0]->children[0]);
            return $parsed;
        }
        
        // collapse an xml node into a php array based on its children
        // for example <node><child>val</child><child2>val2</child2></node>
        // becomes array("child" => "val","child2" => "val2");
        private function simplify($root) {
            $attribs = array();
            if (count($root->children) > 0) {
                foreach ($root->children as $child) {
                    $attribs[strtolower($child->tagname)] = $child->chardata;
                }
                $children = $root->children;
            }
            return $attribs;
        }
        
        // simplify the system information (feeds and types)
        private function simplify_sysinfo($root) {
            $feeds = array();
            $types = array();

            $feednode = $root->children[0];
            $typenode = $root->children[1];
            foreach ($feednode->children as $feed) {
                $tmp = $this->simplify($feed);
                $feeds[$tmp['id']] = $tmp['name'];
            }
            if ($typenode->children) {
                foreach ($typenode->children as $type) {
                    $types[] = $type->chardata;
                }
            }
            
            return array('feeds' => $feeds, 'types' => $types);
        }
        
        // simplify a feed into an array
        // becomes array("info" => array(), "feeds" => array());
        // where each feed is the result of a call to $this->simplify($node)
        private function simplify_feed($root) {
            $parsed = array();
            $rssinfo = array();
            $items = array();
            
            if ($root) {
                foreach ($root->children as $feednode) {
                    if ($feednode->tagname == "CHANNEL")
                        $root = $feednode;
                }
                foreach ($root->children as $something) {
                    if ($something->tagname == "ITEM")
                        $items[] = $this->simplify($something);
                }
                
                $rss = array(
                    "info" => $rssinfo,
                    "items" => $items
                );
                
                $rss['info'] = $this->simplify($root);
                
                return $rss;
            }
            else return array();
        }
        
        // strip out any ampersands...
        private function prepare_xml($xmldata) {
            $find = array(
                "&"
            );
            $replacements = array(
                "&amp;"
            );
            return str_replace($find,$replace,$xmldata);
        }
        
        // parse the xml file
        private function parse_results($xmldata) {
            $xmlparser = xml_parser_create();
            xml_helpers_reinit();
            xml_parser_set_option($xmlparser,XML_OPTION_TARGET_ENCODING, "UTF-8");
            xml_parser_set_option($xmlparser,XML_OPTION_SKIP_WHITE,1);
            
            xml_set_element_handler($xmlparser,"begin_tag_parse","end_tag_parse");
            xml_set_character_data_handler($xmlparser,"char_data");
            
            $xmldata = $this->prepare_xml($xmldata);
            if (!xml_parse($xmlparser,$xmldata))
                throw new ConcertoXMLException(
                    xml_error_string(xml_get_error_code($xmlparser)) . 
                    " on line " . 
                    xml_get_current_line_number($xmlparser)
                );
            xml_parser_free($xmlparser);
        }
        
        private function parse_xml($xmldata,$params) {
            $this->parse_results($xmldata);
            return $this->parse_struct($params);
        }
        
        // perform a query on the concerto server
        // operates over http
        
        /*
        Parameters
            select      [ content | feed | user | system ]
            select_id   [ integer | username ]
            format      [ raw | html | rawhtml | rss | json ]
            orderby     [ id | rand | type_id | mime_type | submitted | start_time | end_time | user_id ]
            range       [ live | future | past | all ]
            count       [ integer ]
            type        [ string ]
            width       [ imgwidth ]
            height      [ imgheight ]
            api         [ apiver ]
        */
        
        private function query(array $params) {            
            $xmlbuf = '';
            $line = null;
            if ($this->_connection) {
                $xmlbuf = $this->_connection->Query($params);
                
                $format = ($params['format'] ? $params['format'] : ConcertoFormats::RSS);
                
                if ($format == ConcertoFormats::RSS) {
                    return $this->parse_xml($xmlbuf,$params);
                }
                else {
                    return $xmlbuf;
                }
            }
            else throw new ConcertoNotConnectedException();
        }
        
        // for testing without connection to a server
        function query_local_file($filename) {
            return $this->parse_xml(file_get_contents($filename));
        }
        
        // shortcut functions so $this->query doesn't have to be used
        //////////////////////////////////////////////////////////////
        
        // this query is just a redirect to the private query function
        function CustomQuery($params) {
            return $this->query($params);
        }
        
        // get a piece of content by it's id
        // auto-return content as HTML
        function GetContent($id, $format = ConcertoFormats::HTML) {
            $content = $this->query(array(
                "select" => "content",
                "select_id" => $id,
                "format" => $format
            ));
            return $content;
        }
        
        // get a list of all content on a feed by it's id
        function GetFeed($id,$limit = NULL) {
            $params = array(
                "select" => "feed",
                "select_id" => $id,
            );
            if ($limit) $params['limit'] = $limit;
            
            return $this->query($params);
        }
        // get a list of available feeds
        function GetFeeds() { 
            $info = $this->GetSystemInfo();
            return $info['feeds'];
        }
        
        // get a list of all content submitted by a user
        function GetUserContent($username,$limit = NULL) {
            $params = array(
                "select" => "user",
                "select_id" => $username
            );
            if ($limit) $params['limit'] = $limit;
            
            return $this->query($params);
        }
        
        
        
        // get information about the system
        function GetSystemInfo() {
            return $this->query(array("select"=>"system"));
        }
    }
?>