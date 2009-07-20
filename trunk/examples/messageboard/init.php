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
	Wax::LoadBlock("database");	
	Wax::LoadBlock("posts");
	Wax::LoadBlock("resources");
?>