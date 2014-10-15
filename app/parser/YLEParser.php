<?php
class YLEParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$items = $this->dom->getElementsByTagName('item');
		foreach($items as $item) {
			$title = $item->getElementsByTagName('title');
			$url = $item->getElementsByTagName('link');
			
			if($title->length == 0 || $url->length == 0)
				continue;
			
			$url = substr($url->item(0)->nodeValue, 13);
			$url = substr($url, 0, strpos($url, "?"));
			
			$titles[] = array('title'=>$title->item(0)->nodeValue, 'url'=>$url);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$container = $this->dom->getElementsByTagName('article');
		if($container->length == 0)
			return $content;
		
		$container = $container->item(0);
		
		$date = $container->getElementsByTagName('time');
		if($date->length > 0) {
			if($date->length > 1)
				$date = $date->item(1);
			else
				$date = $date->item(0);
			
			$timestamp = DateTime::createFromFormat("Y-m-d\TH:i:sT", $date->getAttribute('datetime'));
			$content['timestamp'] = $timestamp->getTimestamp();
		}
        
        $image = $container->getElementsByTagName('img');
        if($image->length > 0 && $image->item(0)->parentNode->getAttribute('class') == "openoverlay") {
            $content['image'] = $image->item(0)->getAttribute('src');
        }
		
		$title = $container->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$content['title'] = utf8_decode($title->item(0)->nodeValue);
		$subTitle = $container->getElementsByTagName('h2');
		if($subTitle->length > 0)
			$content['subTitle'] = utf8_decode($subTitle->item(0)->nodeValue);
		
		$divs = $container->getElementsByTagName('div');
		foreach($divs as $div) {
			if($div->getAttribute('class') == "text") {
				$bodyTextBlocks = $div->getElementsByTagName('p');
				foreach($bodyTextBlocks as $p) {
					$content['bodyText'][] = utf8_decode($p->nodeValue);
				}
				
				break;
			}
		}
		
		return $content;
	}
}