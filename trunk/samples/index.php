<?php
$xmlDoc = new DOMDocument();
$xmlDoc->load("headers.xml");
$x = $xmlDoc->documentElement;

foreach ($x->getElementsByTagName("NavListItem") as $item) {
    print $item->nodeName . " = " . $item->nodeValue . "<br />";
}
?>