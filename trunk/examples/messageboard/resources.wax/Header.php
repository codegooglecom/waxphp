<?php
	// simple view controller: DCIObject implementing View
	class HeaderController extends DCIObject implements View {
		function __construct() {
			parent::__construct(); // initialize parent- need for injection
			
			$viewpath = Wax::LookupPath("fs/app/block/view", array("block" => Wax::GetBlockContext(__FILE__), "view" => "header"));
			echo $this->Render($viewpath, array());
		}
	}
?>