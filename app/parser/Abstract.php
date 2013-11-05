<?php
require_once("app/Loader.php");

abstract class Parser {
	protected $dom;
	
	
	public function __construct($content, $rss=false) {
		$this->dom = new DOMDocument;
		
		$content = str_replace(array("&nbsp;", "\r", "\n", "\t"), " ", $content);
		
		if($rss)
			@$this->dom->loadXML($content);
		else
			@$this->dom->loadHTML($content);
	}
	
	
	protected function CheckDuplicate($list, $url) {
		foreach($list as $item) {
			if($item['url'] == $url)
				return true;
		}
		
		return false;
	}
}
?>
