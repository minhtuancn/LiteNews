<?php
class KalevaParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$headers = $this->dom->getElementById('news-container');
		if($headers == NULL)
			return $titles;
		
		foreach($headers->getElementsByTagName('a') as $link) {
			$titleURL = $link->getAttribute('href');
			$titles[] = array('title'=>$link->nodeValue, 'url'=>$titleURL);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$title = $this->dom->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$content['title'] = $title->item(0)->nodeValue;
		
		$dateContainer = $this->dom->getElementsByTagName('span');
		foreach($dateContainer as $span) {
			if($span->getAttribute('class') == "timestamp") {
				$date = trim(substr($span->nodeValue, 0, strpos($span->nodeValue, '|')));
				
				if(strpos($date, ":") !== false)
					$timestamp = DateTime::createFromFormat("H:i", $date);
				else
					$timestamp = DateTime::createFromFormat("d.m. H:i", $date." 00:00");
				
				if($timestamp instanceof DateTime)
					$content['timestamp'] = $timestamp->getTimestamp();
				break;
			}
		}
		
		$bodyText = $this->dom->getElementsByTagName('isense');
		
		if($bodyText->length == 0)
			return $content;
		
		foreach($bodyText->item(0)->getElementsByTagName('p') as $p) {
			$p = trim($p->nodeValue, " ");
			
			if(!empty($p))
				$content['bodyText'][] = $p;
		}
		
		return $content;
	}
}