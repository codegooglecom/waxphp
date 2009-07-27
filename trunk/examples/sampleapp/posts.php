<?php
	require_once("init.php");
	
	// this here is the application logic:
	
	// we print a header, perform the action, and print a footer.
	// show the header
	new HeaderController();
	
	$controller = new Posts();
		
	new FooterController();
?>