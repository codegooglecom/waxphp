<?php
	class WaxTextNode extends WaxControl {
		function __construct($text) {
			$this->xmlattributes['Text'] = $text;
		}
		function Render($dom) {
			return new DOMText($this->xmlattributes['Text']);
		}
	}
?>