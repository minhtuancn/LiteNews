<?php
class SodexoParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$container = $this->dom->getElementById('food-list-normal');
		if($container == NULL)
			return $titles;
		
		$divs = $container->getElementsByTagName('div');
		foreach($divs as $div) {
			if($div->getAttribute('class') == "day") {
				$titles[] = array('title'=>$div->nodeValue, 'url'=>"/".$div->nodeValue);
			}
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
		
		$titleContainer = $this->dom->getElementsByTagName('body');
		if($titleContainer->length == 0)
			return $content;
		
		$title = $titleContainer->item(0)->getAttribute('class');
		$title = substr($title, strrpos($title, "page-m-") + 7);
		$title = substr($title, 0, strpos($title, " "));
		$title = ucfirst($title);
		$content['title'] = $title;
		
		$container = $this->dom->getElementById('food-list-normal');
		if($container == NULL)
			return $content;
		
		$divs = $container->getElementsByTagName('div');
		$menuContainer = NULL;
		
		foreach($divs as $div) {
			if($div->getAttribute('class') == "day" && $div->nodeValue == $title) {
				$menuContainer = $div;
				break;
			}
		}
		
		if($menuContainer == NULL)
			return $content;
		
		$dateContainer = $container->getElementsByTagName('b');
		if($dateContainer->length == 0)
			break;
		
		$week = intval(substr($dateContainer->item(0)->nodeValue, 7));
		
		switch($content['title']) {
			case "Maanantai":
				$day = 1;
				break;
			case "Tiistai":
				$day = 2;
				break;
			case "Keskiviikko":
				$day = 3;
				break;
			case "Torstai":
				$day = 4;
				break;
			case "Perjantai":
				$day = 5;
				break;
			case "Lauantai":
				$day = 6;
				break;
			case "Sunnuntai":
				$day = 7;
				break;
			default:
				$day = 1;
				break;
		}
		
		$date = new DateTime();
		$date->setISODate(date("Y"), $week, $day);
		$date->setTime(0, 0);
		$content['timestamp'] = $date->getTimestamp();
		
		$menuContainer = $menuContainer->nextSibling;
		
		while($menuContainer->getAttribute('class') == "even" || $menuContainer->getAttribute('class') == "odd") {
			$type = $menuContainer->getElementsByTagName('div')->item(0)->nodeValue;
			
			$menuText = $menuContainer->getElementsByTagName('p');
			foreach($menuText as $p) {
				if($p->getAttribute('class') == 'title') {
					$content['bodyText'][] = $type.": ".$p->nodeValue;
					break;
				}
			}
			
			$menuContainer = $menuContainer->nextSibling;
			if($menuContainer == NULL)
				break;
		}
		
		return $content;
	}
}