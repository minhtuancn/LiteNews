<?php
class DigitodayParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$items = $this->dom->getElementsByTagName('item');
		foreach($items as $item) {
			$title = $item->getElementsByTagName('title');
			$url = $item->getElementsByTagName('link');
			
			if($title->length == 0 || $url->length == 0)
				continue;
			
			$url = substr($url->item(0)->nodeValue, 23);
			$url = substr($url, 0, strpos($url, "?"));
			$titles[] = array('title'=>$title->item(0)->nodeValue, 'url'=>$url);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$container = $this->dom->getElementById('content');
		
		$title = $container->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$content['title'] = $title->item(0)->nodeValue;
		
		$bodyText = $container->getElementsByTagName('p');
		if($bodyText->length == 0)
			return $content;
		
		foreach($bodyText as $p) {
			if($p->getAttribute('class') == "ingress") {
				$date = $p->getElementsByTagName('span');
				
				if($date->length == 0)
					return $content;
				
				$date = $date->item(0);
				$timestamp = DateTime::createFromFormat("d.m.Y H:i", $date->nodeValue);
				if($timestamp == false)
					return $content;
				
				$content['timestamp'] = $timestamp->getTimestamp();
				$content['subTitle'] = $p->nodeValue;
			}
			else {
				if($p->parentNode->getAttribute('class') == "agent vcard")
					break;
				
				$content['bodyText'][] = $p->nodeValue;
			}
		}
		
		return $content;
	}
}
