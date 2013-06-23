<?php
class NewYorkTimesParser extends Parser {
	public function GetTitles() {
		$titles = array();
		$items = $this->dom->getElementsByTagName('item');
		
		foreach($items as $item) {
			$title = $item->getElementsByTagName('title');
			if($title->length == 0)
				continue;
			
			$url = $item->getElementsByTagName('link');
			if($url->length == 0)
					continue;
			
			$url = $url->item(0);
			if($url->hasAttribute('href'))
				$url = $url->getAttribute('href');
			else
				$url = $url->nodeValue;
			
			if(substr($url, 0, 22) == "http://www.nytimes.com") {
				$url = substr($url, 22, strpos($url, "?") - 22);
				$titles[] = array('title'=>$title->item(0)->nodeValue, 'url'=>$url);
			}
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = array(
			'title'=>NULL,
			'subTitle'=>NULL,
			'bodyText'=>array()
		);
		
		$container = $this->dom->getElementById('article');
		if($container == NULL)
			return $content;
		
		$title = $container->getElementsByTagName('nyt_headline');
		if($title->length == NULL)
			return $content;
		else
			$content['title'] = $title->item(0)->nodeValue;
		
		$bodyText = $container->getElementsByTagName('p');
		
		foreach($bodyText as $p) {
			if($p->getAttribute('itemprop') == "articleBody")
				$content['bodyText'][] = $p->nodeValue;
		}
		
		return $content;
	}
}