<?php
    require_once("iConcertoConnection.php");
    
    // very simple connection that just redirects all queries to a local file.
    // the xml in the file is then returned to be parsed by the client
    class ConcertoXMLFileConnection {
        function __construct() {
        }
        
        function Verify() {
            return file_exists("system.xml");
        }   
        
        function Query($params) {
            return file_get_contents("system.xml");
        }
    }
?>