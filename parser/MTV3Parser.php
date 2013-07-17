<?php
class MTV3Parser extends Parser {
	public function GetTitles() {
		$titles = array();
		$items = $this->dom->getElementsByTagName('item');
		
		foreach($items as $item) {
			$title = $item->getElementsByTagName('title');
			$url = $item->getElementsByTagName('link');
	
			if($title->length == 0 || $url->length == 0)
				continue;
			
			$titleURL = substr($url->item(0)->nodeValue, 18);
			$titles[] = array('title'=>$title->item(0)->nodeValue, 'url'=>$titleURL);
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
		
		$container = $this->dom->getElementById('article_section');
		if($container == NULL)
			return $content;
		
		$dateContainer = $container->getElementsByTagName('time');
		foreach($dateContainer as $el) {
			if($el->getAttribute('class') == "dateCreated") {
				if($el->nextSibling->getAttribute('class') == 'dateModified')
					$timestamp = DateTime::createFromFormat("YmdHi", $el->nextSibling->getAttribute('datetime'));
				else
					$timestamp = DateTime::createFromFormat("Y-m-d\TH:i:s.uT", $el->getAttribute('datetime'));
				
				$content['timestamp'] = $timestamp->getTimestamp();
			}
			
			break;
		}
		
		$title = $container->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$bodyText = $this->dom->getElementById('entry-body');
		if($bodyText == NULL)
			return $content;
		
		$bodyText = $bodyText->getElementsByTagName('p');
		if($bodyText->length == 0)
			return $content;
		
		$subTitle = $bodyText->item(0);
		$bodyText->item(0)->parentNode->removeChild($subTitle);
		
		$content['title'] = $title->item(0)->nodeValue;
		$content['subTitle'] = $subTitle->nodeValue;
		
		foreach($bodyText as $p) {
			$p = trim($p->nodeValue, " ");
			
			if(!empty($p))
				$content['bodyText'][] = $p;
		}
		
		return $content;
	}
}