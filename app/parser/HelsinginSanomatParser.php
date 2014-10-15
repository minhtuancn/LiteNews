<?php
class HelsinginSanomatParser extends Parser {
	public function GetTitles() {
		$titles = array();
		$headers = $this->dom->getElementsByTagName('h2');
		
		foreach($headers as $header) {
			if($header->parentNode->parentNode->getAttribute('news-class') == "item-image")
				continue;
			
			$links = $header->getElementsByTagName('a');
			if($links->length == 0)
				continue;
			
			$link = $links->item(0);
			
			if(substr($link->getAttribute('href'), 0, 16) != "http://www.hs.fi" || substr($link->getAttribute('href'), 17, 6) == "videot")
				continue;
			
			$titleURL = substr($link->getAttribute('href'), 16);
			$titleURL = str_replace("%C3%A4", "ä", $titleURL);
			$titleURL = str_replace("%C3%B6", "ö", $titleURL);
			
			$titles[] = array('title'=>utf8_decode($link->nodeValue), 'url'=>$titleURL);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$container = $this->dom->getElementById('main-content');
		if($container == NULL)
			return $content;
		
		$blocks = $container->getElementsByTagName("div");
		if($blocks->length == 0)
			return $content;
		
		$title = $this->dom->getElementById('main-header');
		if($title == NULL)
			return $content;
        
        $content['title'] = utf8_decode($title->nodeValue);
		
		$dateContainer = $container->getElementsByTagname('time');
		if($dateContainer->length > 0) {
			$timestamp = DateTime::createFromFormat("Y-m-d\TH:i:sT", $dateContainer->item(0)->getAttribute('datetime'));
			$content['timestamp'] = $timestamp->getTimestamp();
		}
        
        $divs = $container->getElementsByTagName('div');
        foreach($divs as $div) {
            if(strpos($div->getAttribute('class'), "main-image-area") !== false) {
                $image = $div->getElementsByTagName('img');
                if($image->length > 0) {
                    $content['image'] = $image->item(0)->getAttribute('src');
                    break;
                }
            }
        }
		
		$bodyText = $this->dom->getElementById('article-text-content');
		if($bodyText == NULL)
			return $content;
		
		$bodyText = $bodyText->getElementsByTagName('p');
		if($bodyText->length == 0)
			return $content;
		
		$content['subTitle'] = "";
		
		foreach($bodyText as $p) {
			$p = trim($p->nodeValue, " ");
			
			if(!empty($p))
				$content['bodyText'][] = $p;
		}
		
		return $content;
	}
}