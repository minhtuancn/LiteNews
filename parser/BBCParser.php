<?php
class BBCParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$container = $this->dom->getElementById('container-top-stories-with-splash');
		if($container == NULL)
			return $titles;
		
		foreach($container->getElementsByTagName('a') as $item) {
			$url = $item->getAttribute('href');
			$title = $item->nodeValue;
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
		
		$container = $this->dom->getElementById('blq-main');
		if($container == NULL)
			return $content;
		
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