<?php
	/**
	* Designed to hold information about the PHP $_FILES array
	* This class disables ArraySurrogate's Set function
	*/
	class FilesArr extends ArraySurrogate {
		function Set($index, $val) {
			return;
		}
	}
	/**
	* Designed to hold $_COOKIE information.  Overries Set() and offsetUnset() functions
	* to apply to the actual $_COOKIE array.
	*/
	class CookiesArr extends ArraySurrogate {
		function Set($index, $val) {
			setcookie($index,$val,0);		// create the cookie
		}
		function offsetUnset($offset) {
			setcookie($offset,"",time()-12000); // expire the cookie
		}
	}
	/**
	* Designed to hold $_SESSION information.  Overrides the __construct function
	* to ensure a session has been started.  Also overrides Set() to ensure that 
	* variables are set with session_register()
	*/
	class SessionArr extends ArraySurrogate {
		function __construct($parent) {
			parent::__construct($parent);
			@session_start();
		}
		function Set($index,$val) {
			session_register($index,$val);
			parent::Set($index,$val);
		}
	}
?>