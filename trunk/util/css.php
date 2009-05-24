<?php
	echo "/* rendering stylesheet... */\n";
	$systemp = sys_get_temp_dir();
	echo "/* Sys path: $systemp */\n";
	$path = realpath($systemp . "/" . $_SERVER["QUERYSTRING"]);
	echo "/* Rendering: $path */\n";
	
	if (strpos($path,$systemp) !== false) {
		echo file_get_contents($path);
	}
?>