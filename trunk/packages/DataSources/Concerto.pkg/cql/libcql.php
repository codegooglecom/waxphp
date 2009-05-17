<?
    // CQL parser
    // This file will take a ConcertoQueryLanguage query and convert it into a $_GET api request
    
    // Get content from feed 8
    //      SELECT feed WHERE select_id = 8;
    
    // Get system information
    //      SELECT system;
    
    // Get all content ever on feed 2
    //      SELECT feed WHERE select_id = 8 RANGE all;
    
    // Get 2 random content pieces from feed 2
    //      SELECT feed WHERE select_id = 2 ORDER_BY rand LIMIT 2;
    
    $_COMMANDS = array(
        "SELECT" => array("content","feed","system")
    );
    $_KEYWORDS = array(
        "RANGE" => array("live","future","past","all"),
        "LIMIT" => array("$")
    );
    $_PHRASES = array(
        "ORDER BY" => array("id","rand","type_id","mime_type","submitted","start_time","end_time","user_id"),
        "RETURN AS" => array("raw","html","raw_html","rss","json")
    );
    $_SPECIAL = array(
        " " => "separator",
        "=" => "assignment",
        ";" => "end",
        "'" => "string"
    );
    
    function read_next($query,$pos = 0) {
        $initialpos = $pos;
        while (!$_IMPORTANT_TOKENS[$query[$pos]]) {
            return substr($query,$initialpos,$size);
        }
        
    }
    
    function tokenize($query) {
        
    }
    
    function convert($tokenized_query) {
    
    }

    function concerto_query($query) {
        
    }
?>