<?php
class MuropakettiParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$links = $this->dom->getElementsByTagName('entry');
		foreach($links as $link) {
			$title = $link->getElementsByTagName('title');
			if($title->length == 0)
				continue;
			
			$url = $link->getElementsByTagName('link');
			if($url->length == 0)
				continue;
			
			$url = substr($url->item(0)->getAttribute('href'), 22);
			
			$titles[] = array('title'=>$title->item(0)->nodeValue, 'url'=>$url);
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
		
		$container = $this->dom->getElementById('content');
		if($container == NULL)
			return $content;
		
		$container = $container->getElementsByTagname('div');
		if($container->length == 0)
			return $content;
		
		$container = $container->item(0);
		
		$title = $container->getElementsByTagName('h2');
		if($title->length == 0)
			return $content;
		
		$content['title'] = $title->item(0)->nodeValue;
		
		$dateContainer = $container->getElementsByTagName('p');
		foreach($dateContainer as $date) {
			if($date->getAttribute('class') == "date") {
				$timestamp = DateTime::createFromFormat("d.m.Y H:i", $date->nodeValue." 00:00");
				$content['timestamp'] = $timestamp->getTimestamp();
				break;
			}
		}
		
		$bodyText = $container->getElementsByTagName('p');
		foreach($bodyText as $p) {
			$p = trim($p->nodeValue);
			
			if(!empty($p) && $p != $date->nodeValue)
				$content['bodyText'][] = $p;
		}
		
		return $content;
	}
}
