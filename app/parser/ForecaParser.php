<?php
class ForecaParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$locations = NULL;
		foreach($this->dom->getElementsByTagName('select') as $container) {
			if($container->getAttribute('name') == "loc_id") {
				$locations = $container->getElementsByTagName('option');
			}
		}
		
		if($locations == NULL)
			return $titles;
		
		foreach($locations as $location) {
			if(is_numeric($location->getAttribute('value'))) {
				$titles[] = array(
					'title'=>$location->nodeValue,
					'url'=>"/Finland/".$location->nodeValue."/tenday"
				);
			}
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$title = $this->dom->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$content['title'] = $title->item(0)->nodeValue;
		$content['timestamp'] = time();
		
		$divs = $this->dom->getElementsByTagName('div');
		foreach($divs as $div) {
			if(strpos($div->getAttribute('class'), "c1 daily clr") === 0) {
				$day = $div->getElementsByTagName('span');
				foreach($day as $span) {
					if($span->getAttribute('class') == "h5") {
						$content['bodyText'][] = $span->nodeValue;
						break;
					}
				}
				
				$degrees = "";
				$info = $div->getElementsByTagName('abbr');
				foreach($info as $p) {
					$degrees .= " ".$p->nodeValue;
				}
				$content['bodyText'][] = trim($degrees);
			}
		}
		
		return $content;
	}
}