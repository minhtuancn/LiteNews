<?php
class TheWallStreetJournalParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$items = $this->dom->getElementsByTagName('item');
		foreach($items as $item) {
			$title = $item->getElementsByTagName('title');
			$url = $item->getElementsByTagName('link');
			
			if($title->length == 0 || $url->length == 0)
				continue;
			
			$title = $title->item(0)->nodeValue;
			$url = substr($url->item(0)->nodeValue, 21);
			
			if(strpos($url, "?") !== false)
				$url = substr($url, 0, strpos($url, "?"));
			
			$titles[] = array('title'=>$title, 'url'=>$url);
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
		
		$dateContainer = $this->dom->getElementsByTagName('meta');
		$date = NULL;
		foreach($dateContainer as $meta) {
			if($meta->getAttribute('name') == "article.updated") {
				$date = $meta->getAttribute('content');
				break;
			}
			elseif($meta->getAttribute('name') == "article.published") {
				$date = $meta->getAttribute('content');
			}
		}
		
		$timestamp = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $date);
		$content['timestamp'] = $timestamp->getTimestamp();
		
		$title = $this->dom->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$content['title'] = $title->item(0)->nodeValue;
		
		$subTitleContainer = $this->dom->getElementsByTagName('h2');
		foreach($subTitleContainer as $subTitle) {
			if($subTitle->getAttribute('class') == "subHed deck")
				$content['subTitle'] = $subTitle->nodeValue;
		}
		
		$bodyTextContainer = $this->dom->getElementById('articleBody');
		if($bodyTextContainer == NULL)
			return $content;
		
		foreach($bodyTextContainer->getElementsByTagName('p') as $p) {
			if($p->getAttribute('class') == "") {
				$tickers = $p->getElementsByTagName('span');
				foreach($tickers as $ticker) {
					if($ticker->getAttribute('class') == "article-chiclet down") {
						try {
							$p->removeChild($ticker);
						}
						catch(DOMException $ex) {
							continue;
						}
					}
				}
				
				$content['bodyText'][] = $p->nodeValue;
			}
		}
		
		return $content;
	}
}