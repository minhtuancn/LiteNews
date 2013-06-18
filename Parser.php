<?php
require_once("Loader.php");

abstract class Parser {
	protected $name;
	protected $dom;
	
	
	public function __construct($name, $content, $rss=false) {
		$this->name = $name;
		$this->dom = new DOMDocument;
		
		$content = str_replace(array("&nbsp;", "\r", "\n", "\t"), " ", $content);
		
		if($rss)
			@$this->dom->loadXML($content);
		else
			@$this->dom->loadHTML($content);
	}
}
?>
