<?php
class ItviikkoParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$container = $this->dom->getElementById('tab-1');
		if($container == NULL)
			return $titles;
		
		$container = $container->getElementsByTagName('div');
		if($container->length == 0)
			return $titles;
		
		$links = $container->item(0)->getElementsByTagName('a');
		
		foreach($links as $link) {
			if($link->getAttribute('href') == "#")
				continue;
			
			$titleURL = $link->getAttribute('href');
			$titles[] = array('title'=>$link->nodeValue, 'url'=>$titleURL);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$container = $this->dom->getElementById('col1A');
		if($container == NULL)
			return $content;
		
		$title = $container->getElementsByTagName('h1');
		if($title->length == 0)
			return $content;
		
		$title = $title->item(0);
		$content['title'] = $title->nodeValue;
		$content['subTitle'] = "";
		$subTitleContainer = $title->parentNode->getElementsByTagName('div');
		foreach($subTitleContainer as $div) {
			if($div->getAttribute('class') == "storyCaption") {
				$content['subTitle'] = $div->nodeValue;
				break;
			}
		}
		
		$dateContainer = $container->getElementsByTagName('div');
		foreach($dateContainer as $div) {
			if($div->getAttribute('class') == "time") {
				$date = $div->nodeValue;
				
				if(strpos($date, "(") !== false) {
					$date = substr($date, strrpos($date, " ") + 1, -1);
					if(strlen($date > 5))
						$date = substr($div->nodeValue, 0, strpos($div->nodeValue, " "))." ".$date;
				}
				
				$timestamp = DateTime::createFromFormat("d.m.Y H:i", $date);
				$content['timestamp'] = $timestamp->getTimestamp();
			}
		}
		
		$contentContainer = $container->getElementsByTagName('article');
		if($contentContainer->length == 0)
			return $content;
		
		$contentContainer = $contentContainer->item(0)->getElementsByTagName('div');
		
		foreach($contentContainer as $div) {
		    if($div->getAttribute('class') == "imageContainer") {
		        $image = $div->getElementsByTagName('img');
                if($image->length > 0) {
                    $content['image'] = $image->item(0)->getAttribute('src');
                }
		    }
            
			if($div->getAttribute('class') == "storyText") {
				$bodyText = $div->getElementsByTagName('p');
				
				foreach($bodyText as $p) {
					$p = trim($p->nodeValue, " ");
					
					if(!empty($p))
						$content['bodyText'][] = $p;
				}
				
				break;
			}
		}
		
		return $content;
	}
}