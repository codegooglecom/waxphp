<?php
	// the wax initialization script
	// preferably, the wax directory would be set in the include_path via php.ini 
	// allowing for:
	// 		require_once "wax_init.php";
	// instead of:
	require_once "../../wax_init.php";
	
	// we want to use a database
	mysql_connect("localhost", "root", "");
	mysql_select_db("messageboard");
	
	// load up the blocks we're using
	$posts_block = Wax::LoadBlock("posts");
	$headers_block = Wax::LoadBlock("resources");
	
	// this here is the application logic:
	
	// we print a header, perform the action, and print a footer.
	// show the header
	new HeaderController();
	
	// set up the router
	$router = new QueryStringRouter();
	$controller = $router->controller;
	if (empty($controller))
		$ctrl = new Posts();
	else
		$ctrl = new $controller();
		
	new FooterController();
?>