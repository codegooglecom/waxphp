<?php
	// this class redirects querystring requests into the get/request arrays
	// allows for naming like ?something/edit/3
	// this then becomes ?model=something&action=create&id=3
	class QueryStringRouter {
		var $model;
		var $controller;
		var $ispost;
		
		private $_mappings = array(
			0 => "model",
			1 => "action",
			2 => "id"
		);
		
		function __construct() {
			$pieces = explode("/",$_SERVER['QUERY_STRING']);
			
			if (isset($_POST['action'])) {
			}
			else {
				for ($x = 0; $x < count($pieces); $x++) {
					if (!isset($this->_mappings[$x])) continue;
					else if ($this->_mappings[$x] == "model") {
						$this->controller = $pieces[$x];
						$this->model = $pieces[$x] . "Model";
					}

					$_GET[$this->_mappings[$x]] = $pieces[$x];
					$_REQUEST[$this->_mappings[$x]] = $pieces[$x];
				}
			}
		}
	}
?>