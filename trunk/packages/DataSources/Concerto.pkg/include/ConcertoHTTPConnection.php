<?php
    require_once("iConcertoConnection.php");
    
    // the class that performs a connection to concerto over http
    class ConcertoHTTPConnection implements iConcertoConnection {
        private $_server;
        private $_port;
        private $_suffix;
        
        function __construct($args) {
            $this->_server = $args['server'];
            $this->_port = ($args['port'] ? $args['port'] : 80);
            $this->_suffix = $args['suffix'];
        }
        private function get_connection_string($params) {
            $get = array();
            $paramstr = null;
            
            if ($params) {
                $params['range'] = "all";
                foreach ($params as $key=>$val) {
                    $str = "$key=$val";
                    $get[] = $str;
                }
                $paramstr = implode("&",$get);
            }
        
            $str = $this->_server;
            if ($this->_port != 80)
                $str .= ":" . $this->_port;
                
            $str .= $this->_suffix;
            $str .= ($paramstr ? "/?" . $paramstr : '');
            return $str;
        }
        function Verify() {
            echo "trying to open " . ($this->get_connection_string(null,false)) . "<br>";
            flush();
            ob_flush();
            if (fopen($this->get_connection_string(null,false),'r')) return true;
            else return false;
        }
        function Query($params) {
            $query = $this->get_connection_string($params);
            echo $query . "<br>";
            $fd = fopen($query, 'r');
            while ($line = fread($fd,4096)) 
                $xmlbuf .= $line;
            fclose($fd);
            
            return $xmlbuf;
        }
    }
?>