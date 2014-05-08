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
			
			$titleURL = substr($url->item(0)->nodeValue, 17);
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
		
		$container = $this->dom->getElementById('top');
		if($container == NULL)
			return $content;
		
		$dateContainer = $container->getElementsByTagName('time');
		foreach($dateContainer as $el) {
			if($el->getAttribute('class') == "dateCreated") {
				if($el->nextSibling != NULL && $el->nextSibling->getAttribute('class') == 'dateModified')
					$timestamp = DateTime::createFromFormat("Y-m-d\TH:i:s.uT", $el->nextSibling->getAttribute('datetime'));
				else
					$timestamp = DateTime::createFromFormat("Y-m-d\TH:i:s.uT", $el->getAttribute('datetime'));
				
				$content['timestamp'] = $timestamp->getTimestamp();
				break;
			}
		}

		$title = $container->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;

		$bodyTextContainer = $this->dom->getElementsByTagName('div');
		if($bodyTextContainer->length == 0)
			return $content;

		foreach($bodyTextContainer as $div) {
			if($div->getAttribute('class') == "article-66") {
				$bodyText = $div->getElementsByTagName('p');
				
				if($bodyText->length == 0)
					return $content;
			}
		}

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