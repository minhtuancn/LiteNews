<?php
require_once("app/Loader.php");

abstract class Parser {
	protected $dom;
	
	
	public function __construct($content, $rss=false) {
		$this->dom = new DOMDocument;
		
		$content = str_replace(array("&nbsp;", "\r", "\n", "\t"), " ", $content);
		$content = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $content);
		
		libxml_use_internal_errors(true);
		
		if($rss)
			$this->dom->loadXML($content);
		else
			$this->dom->loadHTML($content);
		
		libxml_use_internal_errors(false);
	}
	
	
	protected function InitArticle() {
		return array(
			'title'=>NULL,
			'image'=>NULL,
			'subTitle'=>NULL,
			'bodyText'=>array(),
			'timestamp'=>0
		);
	}
	
	protected function CheckDuplicate($list, $url) {
		foreach($list as $item) {
			if($item['url'] == $url)
				return true;
			elseif(strpos($url, "?") !== false) {
				if($item['url'] == substr($url, 0, strpos($url, "?")))
					return true;
			}
		}
		
		return false;
	}
}
?>
