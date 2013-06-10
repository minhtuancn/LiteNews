<?php
class Parser {
	private $name;
	private $dom;
	
	
	public function __construct($name, $content, $rss=false) {
		$this->name = $name;
		$this->dom = new DOMDocument;
		
		$content = str_replace(array("&nbsp;", "\r", "\n", "\t"), " ", $content);
		
		if($rss)
			@$this->dom->loadXML($content);
		else
			@$this->dom->loadHTML($content);
	}
	
	
	public function GetTitles() {
		$titles = array();
		
		switch($this->name) {
			case "Iltalehti":
				$headers = $this->dom->getElementsByTagName('h1');
				
				foreach($headers as $header) {
					$links = $header->getElementsByTagName('a');
					
					if($links->length == 0)
						continue;
					
					$titleURL = '?page=Iltalehti&amp;href='.$links->item(0)->getAttribute('href');
					$title = "";
					
					foreach($header->getElementsByTagName('span') as $titlePart)
						$title .= $titlePart->nodeValue." ";
					
					$titles[] = array('title'=>$title, 'url'=>$titleURL);
				}
				
				break;
			
			case "Iltasanomat":
				$headers = $this->dom->getElementsByTagName('h2');
				
				foreach($headers as $header) {
					$links = $header->getElementsByTagName('a');
					
					if($links->length == 0)
						continue;
					
					$link = $links->item(0);
					$titleURL = '?page=Iltasanomat&amp;href='.$link->getAttribute('href');
					$titles[] = array('title'=>$link->nodeValue, 'url'=>$titleURL);
				}
				
				break;
			
			case "Kaleva":
				$headers = $this->dom->getElementById('news-container');
				
				if($headers == NULL)
					break;
				
				foreach($headers->getElementsByTagName('a') as $link) {
					$titleURL = '?page=Kaleva&amp;href='.$link->getAttribute('href');
					$titles[] = array('title'=>$link->nodeValue, 'url'=>$titleURL);
				}
				
				break;
			
			case "Helsingin Sanomat":
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
					
					$titleURL = '?page=Helsingin Sanomat&amp;href='.substr($link->getAttribute('href'), 16);
					$titles[] = array('title'=>utf8_decode($link->nodeValue), 'url'=>$titleURL);
				}
				
				break;
			
			case "It-viikko":
				$container = $this->dom->getElementById('tab-1');
				
				if($container == NULL)
					break;
				
				$container = $container->getElementsByTagName('div');
				
				if($container->length == 0)
					break;
				
				$links = $container->item(0)->getElementsByTagName('a');
				
				foreach($links as $link) {
					if($link->getAttribute('href') == "#")
						continue;
					
					$titleURL = '?page=It-viikko&amp;href='.$link->getAttribute('href');
					$titles[] = array('title'=>$link->nodeValue, 'url'=>$titleURL);
				}
				
				break;
			
			case "MTV3":
				$items = $this->dom->getElementsByTagName('item');
				
				foreach($items as $item) {
					$title = $item->getElementsByTagName('title');
					$url = $item->getElementsByTagName('link');
	
					if($title->length == 0 || $url->length == 0)
						continue;
					
					$titleURL = '?page=MTV3&amp;href='.substr($url->item(0)->nodeValue, 18);
					$titles[] = array('title'=>$title->item(0)->nodeValue, 'url'=>$titleURL);
				}
				break;
		}
		
		return $titles;
	}
	
	public function GetArticle() {
		$content = array(
			'title'=>'Sivun lataus epäonnistui',
			'subTitle'=>'Sivun lataus epäonnistui',
			'bodyText'=>array()
		);
		
		switch($this->name) {
			case "Iltalehti":
				$title = $this->dom->getElementsByTagName('h1');
				$contentBox = $this->dom->getElementsByTagName('isense');
				
				if($title->length == 0 || $contentBox->length == 0)
					break;
				
				$contentBox = $contentBox->item(0)->getElementsByTagName('p');
				
				if($contentBox->length == 0)
					break;
				
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
				
				break;
			
			case "Iltasanomat":
				$title = $this->dom->getElementsByTagName('h1');
				$contentBox = $this->dom->getElementByID('article-text');
				
				if($title->length == 0 || $contentBox == NULL)
					break;
				
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
				
				break;
			
			case "Kaleva":
				$title = $this->dom->getElementsByTagName('h1');
				
				if($title->length == 0)
					break;
				
				$content['title'] = $title->item(0)->nodeValue;
				$content['subTitle'] = "";
				$bodyText = $this->dom->getElementsByTagName('isense');
				
				if($bodyText->length == 0)
					break;
				
				foreach($bodyText->item(0)->getElementsByTagName('p') as $p) {
					$p = trim($p->nodeValue, " ");
					
					if(!empty($p))
						$content['bodyText'][] = $p;
				}
				
				break;
			
			case "Helsingin Sanomat":
				$container = $this->dom->getElementByID('main-content');
				
				if($container == NULL)
					break;
				
				$blocks = $container->getElementsByTagName("div");
				
				if($blocks->length == 0)
					break;
				
				$title = new DOMNodeList; // In case of foreach don't find any matches
				
				foreach($blocks as $block) {
					if(trim($block->getAttribute('class'), " ") == "full-article-top") {
						$title = $block->getElementsByTagName('h1');
						break;
					}
				}
				
				if($title->length == 0)
					break;
				
				$bodyText = $this->dom->getElementByID('article-text-content');
				
				if($bodyText == NULL)
					break;
				
				$bodyText = $bodyText->getElementsByTagName('p');
				
				if($bodyText->length == 0)
					break;
				
				$content['title'] = utf8_decode($title->item(0)->nodeValue);
				$content['subTitle'] = "";
				
				foreach($bodyText as $p) {
					$p = trim($p->nodeValue, " ");
					
					if(!empty($p))
						$content['bodyText'][] = utf8_decode($p);
				}
				
				break;
			
			case "It-viikko":
				$container = $this->dom->getElementById('col1A');
				
				if($container == NULL)
					break;
				
				$title = $container->getElementsByTagName('h1');
				
				if($title->length == 0)
					break;
				
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
				
				$contentContainer = $container->getElementsByTagName('article');
				
				if($contentContainer->length == 0)
					break;
				
				$contentContainer = $contentContainer->item(0)->getElementsByTagName('div');
				
				foreach($contentContainer as $div) {
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
				
				break;
			
			case "MTV3":
				$container = $this->dom->getElementById('article_section');
				
				if($container == NULL)
					break;
				
				$title = $container->getElementsByTagName('h1');
				
				if($title->length == 0)
					break;
				
				$bodyText = $this->dom->getElementById('entry-body');
				
				if($bodyText == NULL)
					break;
				
				$bodyText = $bodyText->getElementsByTagName('p');
				
				if($bodyText->length == 0)
					break;
				
				$subTitle = $bodyText->item(0);
				$bodyText->item(0)->parentNode->removeChild($subTitle);
				
				$content['title'] = $title->item(0)->nodeValue;
				$content['subTitle'] = $subTitle->nodeValue;
				
				foreach($bodyText as $p) {
					$p = trim($p->nodeValue, " ");
					
					if(!empty($p))
						$content['bodyText'][] = $p;
				}
				
				break;
		}
		
		return $content;
	}
}
?>
