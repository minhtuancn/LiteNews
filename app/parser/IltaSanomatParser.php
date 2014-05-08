<?php
class IltaSanomatParser extends Parser {
	public function GetTitles() {
		$titles = array();
		
		$headers = $this->dom->getElementsByTagName('h2');
		
		foreach($headers as $header) {
			$links = $header->getElementsByTagName('a');
			if($links->length == 0)
				continue;
			
			$link = $links->item(0);
			$titleURL = $link->getAttribute('href');
			
			if(substr($titleURL, 0, 4) == "http")
				continue;
			
			if(strpos($titleURL, "?") !== false)
				$titleURL = substr($titleURL, 0, strpos($titleURL, "?"));
			
			$newTitle = array('title'=>$link->nodeValue, 'url'=>$titleURL);
			
			if(!$this->CheckDuplicate($titles, $newTitle['url']))
				$titles[] = $newTitle;
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
		$contentBox = $this->dom->getElementByID('article-text');
		
		if($title->length == 0 || $contentBox == NULL)
			return $content;
		
		$dateContainer = $this->dom->getElementById('article-main')->getElementsByTagName('div')->item(0)->getElementsByTagName('div')->item(0);
		if($dateContainer != NULL && strpos($dateContainer->getAttribute('class'), "date") !== false) {
			$date = trim($dateContainer->nodeValue);
			
			if(strpos($date, "päivitetty") === false)
				$date = substr($date, strpos($date, ":") + 2);
			else
				$date = substr($date, strpos($date, "y:") + 3);
			
			$timestamp = DateTime::createFromFormat("d.m.Y H:i", $date);
			$content['timestamp'] = $timestamp->getTimestamp();
		}
		
		$content['title'] = $title->item(0)->nodeValue;
		$subTitle = $contentBox->getElementsByTagName('p');
		
		if($subTitle->length > 0 && trim($subTitle->item(0)->getAttribute('class'), " ") == "ingress") {
			$content['subTitle'] = $subTitle->item(0)->nodeValue;
			$contentBox->removeChild($subTitle->item(0));
		}
		
		$bodyText = str_replace(array("<br/>", "<br />"), "<br>", $this->dom->saveXML($contentBox));
		$bodyText = strip_tags($bodyText, "<p><li><br>");
		$bodyText = str_replace(array("<p>", "</li>"), "", $bodyText);
		$bodyText = str_replace(array("</p>", "<li>"), "<br>", $bodyText);
		$bodyText = explode("<br>", $bodyText);
		
		foreach($bodyText as $p) {
			$p = trim($p, " ");
			
			if(!empty($p))
				$content['bodyText'][] = $p;
		}
		
		return $content;
	}
}