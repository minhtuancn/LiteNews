<?php
class IltalehtiParser extends Parser {
	public function GetTitles() {
		$titles = array();
		$headers = $this->dom->getElementsByTagName('h1');
		
		foreach($headers as $header) {
			$links = $header->getElementsByTagName('a');
			if($links->length == 0)
				continue;
			
			$titleURL = $links->item(0)->getAttribute('href');
			$title = "";
			
			if(substr($titleURL, 0, 4) == "http")
				continue;
			
			foreach($header->getElementsByTagName('span') as $titlePart)
				$title .= $titlePart->nodeValue." ";
			
			if(!$this->CheckDuplicate($titles, $titleURL))
				$titles[] = array('title'=>$title, 'url'=>$titleURL);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = $this->InitArticle();
		
		$title = $this->dom->getElementsByTagName('h1');
		$contentBox = $this->dom->getElementsByTagName('isense');
		
		if($title->length == 0 || $contentBox->length == 0)
			return $content;
		
		$dateContainer = $title->item(0)->parentNode->getElementsByTagName('p');
		foreach($dateContainer as $p) {
			if(strpos($p->getAttribute('class'), "juttuaika") !== false) {
				$datetime = trim(substr($p->nodeValue, strpos($p->nodeValue, " ") + 1));
				$date = substr($datetime, 0, strpos($datetime, " "));
				$time = substr($datetime, strrpos($datetime, "klo") + 4, 5);
				$timestamp = DateTime::createFromFormat("d.m.Y H.i", $date." ".$time);
				if($timestamp instanceof DateTime)
					$content['timestamp'] = $timestamp->getTimestamp();
				
				break;
			}
		}
		
		$contentBox = $contentBox->item(0)->getElementsByTagName('p');
		if($contentBox->length == 0)
			return $content;
		
		$content['title'] = $title->item(0)->nodeValue;
		
		foreach($contentBox as $p) {
			if(trim($p->getAttribute('class'), " ") == "ingressi")
				$content['subTitle'] = $p->nodeValue;
			elseif(trim($p->parentNode->getAttribute('class'), " ") != "kainalo kuvaright") {
				$p = trim($p->nodeValue, " ");
				
				if(!empty($p))
					$content['bodyText'][] = $p;
			}
		}
		
		return $content;
	}
}