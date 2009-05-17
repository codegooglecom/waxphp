<?php
	require_once("../init.php");
	
	class SamplePage extends Page {	
		function first() {
			echo "Ran the first action!<br />";
		}
		function second() {
			echo "Ran the second action!<br />";
		}
	}
	
	$page = new SamplePage("master.waxml");
	$page->Render();
?>