<?php
	require_once("XMLControl.php");

	/**
	* The WaxControl class defines an object that 
	* can be defined via XML.  The object does
	* not produce a renderable output in HTML
	* format.  Use this when you need to create
	* container classes or data classes.
	*
	* @author Joe Chrzanowski
	*/
	class WaxControl extends XMLControl {		
		/**
		* Constructor -- basically just sets a few basic housekeeping things for the control
		*/
		function __construct($tagName, $attributes = null) {
			parent::__construct($tagName, null, $attributes);
			
		}
		
		/**
		* The RenderChildren function of a WaxControl returns an array of stuff to render.
		*/
		function RenderChildren(DOMDocument $doc) {
			$elems = array();
			foreach ($this->_children as $child) {
				$elems[] = $child->Render($doc);
			}
			
			return $elems;
		}	
		
		/**
		* WaxControls simply fit into the document structure.  They do not actually render anything
		* except for their own children.
		*/
		function Render(DOMDocument $doc) {
			if ($this->parentNode == null)
				throw new Exception("ERROR: WaxControl cannot be the root of the document.  Use WaxWebControl instead.");
		
			if (method_exists($this,"PreRender")) {
				$this->PreRender($doc);
			}
			
			return $this->RenderChildren($doc);
		}
	}
?>