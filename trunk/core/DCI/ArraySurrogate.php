<?php
	class ArraySurrogate implements ArrayAccess {
		private $_parent = NULL;
		function __construct($parent) { 
			$this->_parent &= $parent; 
		}
		
		function Get($index) {
			return $this->_parent[$index];
		}
		function Set($index,$val) {
			$this->_parent[$index] = $val;
		}
		
		function offsetExists($offset) {
			return isset($this->_parent[$offset]);
		}
		function offsetGet($offset) {
			return $this->Get($offset);
		}
		function offsetSet($offset,$value) {
			$this->Set($offset,$value);
		}
		function offsetUnset($offset) {
			unset($this->_parent[$offset]);
		}
		function __toString() {
			return "<pre>" . print_r($this->_parent,true) . "</pre>";
		}
	}
?>