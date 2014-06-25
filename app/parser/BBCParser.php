<?php
class BBCParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$container = $this->dom->getElementById('top-stories');
		
		if($container == NULL)
			return $titles;
		
		foreach($container->getElementsByTagName('a') as $item) {
			$url = $item->getAttribute('href');
			$title = $item->nodeValue;
			
			if(strpos($url, "/sport/0/") === false && !$this->CheckDuplicate($titles, $url))
				$titles[] = array('title'=>$title, 'url'=>$url);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$container = $this->dom->getElementById('page');
		if($container == NULL)
			return $content;
		
		$dateContainer = $container->getElementsByTagName('p');
		foreach($dateContainer as $p) {
			if($p->getAttribute('class') == "date date--v1") {
				$content['timestamp'] = $p->getAttribute('data-seconds');
				break;
			}
		}
		
		$title = $container->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		else
			$content['title'] = $title->item(0)->nodeValue;
		
		if($container->getElementsByTagName('p')->length == 0)
			return $content;
		
		$blocks = $container->getElementsByTagName('p');
		
		foreach($blocks as $block) {
			if(trim($block->getAttribute('class')) == "introduction")
					$content['subTitle'] = $block->nodeValue;
			elseif($block->nodeValue != "Please turn on JavaScript. Media requires JavaScript to play.")
				$content['bodyText'][] = $block->nodeValue;
		}
		
		return $content;
	}
}