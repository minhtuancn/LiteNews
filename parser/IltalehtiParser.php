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
			
			foreach($header->getElementsByTagName('span') as $titlePart)
				$title .= $titlePart->nodeValue." ";
			
			$titles[] = array('title'=>$title, 'url'=>$titleURL);
		}
		
		return $titles;
	}
	
	
	public function GetArticle() {
		$content = array(
			'title'=>NULL,
			'subTitle'=>NULL,
			'bodyText'=>array()
		);
		
		$title = $this->dom->getElementsByTagName('h1');
		$contentBox = $this->dom->getElementsByTagName('isense');
		
		if($title->length == 0 || $contentBox->length == 0)
			return $content;
		
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