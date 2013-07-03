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
		$content = array(
			'title'=>NULL,
			'subTitle'=>NULL,
			'bodyText'=>array(),
			'timestamp'=>0
		);
		
		$title = $this->dom->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$content['title'] = $title->item(0)->nodeValue;
		$content['subTitle'] = "";
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