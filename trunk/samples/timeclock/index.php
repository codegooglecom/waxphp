<?php
	// this would normally be set via an include_path
	require_once("../init.php");
	
	// define the data source
	$dsconn = new DatabaseConnection("mysql:host=localhost;dbname=waxTimeclock","root","");
	
	class TimeClock extends Page {
		function punchIn() {
			$form = $this->GetForm("punchForm");
			
		}
		function punchOut() {
			
		}
		function punch() {
		
		}
		function report() {
			
		}
	}
	
	$page = new TimeClock("templates/timeclock.waxml");
	$page->ApplyTheme("defaultblue");
	$page->Render();
?>