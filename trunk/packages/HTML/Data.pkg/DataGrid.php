<?php
	/**
	* The DataGrid class.  This class displays a table
	* of data from a database.  The table also embeds itself
	* inside a WISP form, allowing for CRUD operations using
	* the datasource bound to the data grid.
	*
	* @author Joe Chrzanowski
	*/
	class DataGrid extends HTMLControl {
		private $_ds = null;
		
		function __construct() {
			parent::__construct("table");
			$this->htmlattributes->class = "wtable";
		}
		
		function drawCreate($page) {
			$form = new WForm();
			$form->xmlattributes->ID = $this->xmlattributes->ID . "CreateForm";
			$form->xmlattributes->Action = $page->LinkTo(array("action" => null));
		}
		function drawRetrieve($page) {
		}
		function drawRetrieveAll($page) {
			$datarecords = $ds->RetrieveAll($page->GetForm($this->xmlattributes->ID . "_form"));
				
			$printedheaders = false;
			foreach ($datarecords as $datarecord) {
				if (!$printedheaders) {
					$headers = new HTMLControl("tr");
					foreach ($datarecord->GetAll() as $col=>$val) {
						$headers->AddChild(new HTMLControl("th",$col));
					}
					$headers->AddChild(new HTMLControl("th","&nbsp;"));
					$this->AddChild($headers);
					
					$printedheaders = true;
				}
				// create the row
				$tr = new HTMLControl("tr");
				
				foreach ($datarecord->GetAll() as $col => $val) {
					$td = new HTMLControl("td",$val);
					$tr->AddChild($td);
				}
				$actions = new HTMLControl("td","<a href='#'>edit</a> | <a href='#'>delete</a>");
				$tr->AddChild($actions);
				
				// add the row to the table
				$this->AddChild($tr);
			}
		}
		function drawUpdate($page) {
		}
		function drawDelete($page) {
		}
		
		function OnConstruct($page) {
			// look thru children, see if any templates were redefined
			
			// make sure the datasource is valid
			
			// figure out which action we're actually working on
			
			// draw the necessary table
			$ds = $page->LookupControl($this->xmlattributes->DataSource);
			if ($ds) {
				$this->_ds = $ds;
				switch ($page->get['action']) {
					case $this->xmlattributes->ID . "_create":
						$this->drawCreate($page);
					break;
					case $this->xmlattributes->ID . "_retrieve":
						$this->drawRetrieve($page);
					break;
					case $this->xmlattributes->ID . "_update":
						$this->drawUpdate($page);
					break;
					case $this->xmlattributes->ID . "_delete":
						$this->drawDelete($page);
					break;
					default:
						
				}
			}		
		}
		function Build() {
			
		}
	}
?>