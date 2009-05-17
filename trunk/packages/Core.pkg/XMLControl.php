<?php
	/**
	* This class is responsible for representing a node in a *.waxml document.
	* XMLControls are responsible only for maintaining a document structure - 
	* they are not responsible for any processing or rendering of content.
	* If you need to create a custom control, look at the WaxWebControl class.
	*
	* @author Joe Chrzanowski
	*/
	class XMLControl {
		protected $_wax_node_id = null;
		protected $_allow_attribute_magic_creation = false;
		
		var $tagName = null;
		var $parentNode = null;
		var $value = null;
		var $xmlattributes = array();
		protected $_children = array();
		
		function __construct($tagName, $value = null, $attributes = null) {
			$this->tagName = $tagName;
			$this->value = null;
			
			// for easily identifying nodes and making sure
			// each node is unique.
			$this->_wax_node_id = md5(microtime(true) . rand());
			
			if (is_array($attributes)) {
				$this->attributes = $attributes;
			}
		}
		
		/**
		* GetWaxNodeID() - this function returns the XMLControl's internal
		* wax id.  This ID should be unique for each XMLControl in the 
		* current working space.
		*/
		function GetWaxNodeID() {
			return $this->_wax_node_id;
		}
		
		/**
		* AddChild - responsible for adding a child to this node as 
		* a part of the XML Document structure
		*/
		function AddChild(XMLControl $child) {
			$nodeid = $child->GetWaxNodeID();
			$child->parentNode = $this;
			$this->_children[$nodeid] = $child;
		}
		
		/**
		* Get Child returns a child of the current node that matches
		* the passed information
		*/
		function GetChild(XMLControl $child) {
			$childid = $child->GetWaxNodeID();
			return $this->GetChildByWaxNodeID();
		}
		
		/**
		* Removes a child from the control
		*/
		function RemoveChild(XMLControl $child) {
			$childid = $child->GetWaxNodeID();
			if (isset($this->_children[$childid])) {
				$this->_children[$childid] = null;
				unset($this->_children[$childid]);
			}
		}
		
		/**
		* Gets the children of the XML Control
		*/
		function GetChildren() {
			return $this->_children;
		}
		
		/**
		* Get Child by WaxID returns a node based on it's actual Wax ID
		*/
		function GetChildByWaxNodeID($childid) {
			if (isset($this->_children[$childid])) {
				return $this->_children[$childid];
			}
			else return null;
		}
		
		/**
		* Get Children by TagName returns all children with a specific tag name
		*/
		function GetChildrenByTagName($tag) {
			$ret = array();
			foreach ($this->_children as $child) {
				if ($child->tagName == $tag) $ret[] = $child;
			}
			return $ret;
		}
		
		
		
		/*********************************************
		* Magic functions							 *
		**********************************************/
		
		
		
		/**
		* Attribute accessor function 
		*
		* @param string $var The attribute name to fetch.
		*/
		function __get($var) {
			if (isset($this->attributes[$var])) {
				return $this->attributes[$var];
			}
			else return false;
		}
		/**
		* Attribute set function
		*
		* @param string $var The attribute name to set
		* @param mixed $value The value to set the attribute to.
		*/
		function __set($var, $value) {
			if (isset($this->attributes[$var]) || $this->_allow_attribute_magic_creation) {
				$this->attributes[$var] = $value;
			}
		}
	}
?>