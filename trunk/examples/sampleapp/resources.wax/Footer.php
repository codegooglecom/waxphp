<?php
	// simple view controller: DCIObject implementing View
	class FooterController extends DCIObject implements View {
		function __construct() {
			parent::__construct(); // initialize parent- need for injection
			
			$block = Wax::GetBlock("resources");
			echo $this->Render($block->views('footer'), array());
		}
	}
?>