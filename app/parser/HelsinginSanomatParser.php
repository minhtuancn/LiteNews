<?php
class HelsinginSanomatParser extends Parser {
	public function GetTitles() {
		$titles = array();
		$headers = $this->dom->getElementsByTagName('h2');
		
		foreach($headers as $header) {
			if($header->parentNode->parentNode->getAttribute('news-class') == "item-image")
				continue;
			
			$links = $header->getElementsByTagName('a');
			if($links->length == 0)
				continue;
			
			$link = $links->item(0);
			
			if(substr($link->getAttribute('href'), 0, 16) != "http://www.hs.fi" || substr($link->getAttribute('href'), 17, 6) == "videot")
				continue;
			
			$titleURL = substr($link->getAttribute('href'), 16);
			$titleURL = str_replace("%C3%A4", "ä", $titleURL);
			$titleURL = str_replace("%C3%B6", "ö", $titleURL);
			
			$titles[] = array('title'=>utf8_decode($link->nodeValue), 'url'=>$titleURL);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = array(
			'title'=>NULL,
			'subTitle'=>NULL,
			'bodyText'=>array(),
			'timestamp'=>0
		);
		
		$container = $this->dom->getElementByID('main-content');
		if($container == NULL)
			return $content;
		
		$blocks = $container->getElementsByTagName("div");
		if($blocks->length == 0)
			return $content;
		
		$title = new DOMNodeList; // In case of foreach don't find any matches
		
		foreach($blocks as $block) {
			if(trim($block->getAttribute('class'), " ") == "full-article-top") {
				$title = $block->getElementsByTagName('h1');
				break;
			}
		}
		
		if($title->length == 0)
			return $content;
		
		$dateContainer = $container->getElementsByTagname('time');
		if($dateContainer->length > 0) {
			$timestamp = DateTime::createFromFormat("Y-m-d\TH:i:sT", $dateContainer->item(0)->getAttribute('datetime'));
			$content['timestamp'] = $timestamp->getTimestamp();
		}
		
		$bodyText = $this->dom->getElementByID('article-text-content');
		if($bodyText == NULL)
			return $content;
		
		$bodyText = $bodyText->getElementsByTagName('p');
		if($bodyText->length == 0)
			return $content;
		
		$content['title'] = utf8_decode($title->item(0)->nodeValue);
		$content['subTitle'] = "";
		
		foreach($bodyText as $p) {
			$p = trim($p->nodeValue, " ");
			
			if(!empty($p))
				$content['bodyText'][] = utf8_decode($p);
		}
		
		return $content;
	}
}