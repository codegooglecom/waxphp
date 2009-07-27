<?php
	// simple view controller: DCIObject implementing View
	class HeaderController extends DCIObject implements View {
		function __construct() {
			parent::__construct(); // initialize parent- need for injection
			
			
			$args = array('js'=>array(),'css'=>array());
			
			foreach (Wax::GetLoadedBlocks() as $name => $block) {
				foreach ($block->js as $script) {
					$args['js'][] = $script;
				}
				foreach ($block->css as $css) {
					$args['css'][] = $css;
				}
			}
			
			$block = Wax::GetBlock("resources");
			echo $this->Render($block->views('header'), $args);
		}
	}
?>