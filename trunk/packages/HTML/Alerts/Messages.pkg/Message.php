<?
    /***********************************************
     * CGL/Message : For displaying the different
     * types of messages
     * ---------------------------------------------
     * (c) 2007 - Joe Chrzanowski
     * ---------------------------------------------
     * This is for displaying different types of  
     * messages on screen while making them look 
     * pretty.
     ***********************************************/
     
    class Message extends HTMLControl {
        private $_title;
        private $_message;
        
        function __construct($title,$message) {
            parent::__construct("div");
            
            $this->_title = $title;
            $this->_message = $message;
            
            // Create the header and render it
            $header = new HTMLElement("div", $this->_title);
            $header->attributes->Set('class','message_title');
            $this->AddChild($header);
            
            // Create the text and render it
            $message = new HTMLElement("div", $this->_message);
            $message->attributes->Set('class','message_text');
            $this->AddChild($message);
        }
        function Show() { $this->Render(); }
    }
?>