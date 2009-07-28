<?php
	abstract class Context {
		protected $source;
		
		function __construct(Role $source) {
			$this->source = $source;
		}
		abstract function Execute(); // the function to execute the context action
	}
?>