<?php
	/**
	* The WaxWebControl class defines an object 
	* that can be defined via XML and which 
	* produces a DOM object when called for rendering
	*
	* The DOM object will generally be the representation
	* of an HTML element to display in the final document.
	*
	* The constructor takes an HTML tag as the tagName
	* Usually an inherited class will define this.
	*
	* @author Joe Chrzanowski
	*/
	class WaxWebControl extends XMLControl {
		public $htmlattributes = array();
		public $innerHTML = null;
		
		function __construct($tagName, $innerHTML = null, $defaultAttributes = null) {
			parent::__construct($tagName, $defaultAttributes);
			$this->innerHTML &= $this->value;
			if (!is_null($innerHTML))
				$this->SetHTML($innerHTML);
		}
		
		/**
		* Set the innerHTML of the control
		*/
		function SetHTML($html) {
			$this->value = $html;
		}
		
		/**
		* Append HTML to the current innerHTML
		*/
		function AddHTML($html) {
			$this->value .= $html;
		}


		/**
		* The flattenChildren function is necessary for making WaxControls act as they should.
		* The function basically takes a tree, returned from a WaxControl's RenderChildren() function,
		* and recurses through it to create an array of the children.
		*
		* This allows WaxControls to act as container classes
		*
		* @param array $arr The tree of children to flatten
		*/
		private function flattenChildren($arr) {
			$ret = array();
			foreach ($arr as $obj) {
				if (is_array($obj)) {
					$ret = array_merge($ret, $this->flattenChildren($obj));
				}
				else {
					$ret[] = $obj;
				}	
			}
			return $ret;
		}

		/**
		* The RenderChildren function adds children 
		* to the rendered control
		*/	
		function RenderChildren(DOMDocument $doc, DOMNode $ctrl) {
			// and append any children that this node has.
			foreach ($this->_children as $child) {
				$cr_res = $child->Render($doc);
				
				if ($cr_res instanceof DOMNode) {
					$ctrl->appendChild($cr_res);
				}
				else if (is_array($cr_res)) {					
					$cr_res = $this->flattenChildren($cr_res);
					
					foreach ($cr_res as $cchild) {
						if ($cchild instanceof DOMNode) {
							$ctrl->appendChild($cchild);
						}
					}
				}
				else continue;
			}
			
			return $ctrl;
		}
		
		/**
		* The Render function is used to retrieve this XMLControl
		* class as a DOM Element for use in rendering.
		*/
		function Render(DOMDocument $doc) {	
			// primarily for last minute manipulation
			if (method_exists($this,"PreRender"))
				$this->PreRender();
				
			// create the DOMNode and set the attributes
			$ctrl = $doc->createElement($this->tagName, $this->value);
			foreach ($this->htmlattributes as $attrib => $value) {
				$ctrl->setAttribute($attrib,$value);
			}
			
			// add the children to this control
			$ctrl = $this->RenderChildren($doc, $ctrl);
			
			
			// primarily for creating script bindings
			if (method_exists($this,"PostRender"))
				$this->PostRender($doc);
							
			return $ctrl;
		}
	}
?>