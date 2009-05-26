<?php
	require_once("../init.php");
	
	class SamplePage extends Page {	
		// I don't even need anything here
	}
	
	$page = new SamplePage("master.waxml");
	$page->Render();
?>