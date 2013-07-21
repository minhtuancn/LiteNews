<?php
require_once("Parser.php");

class ArticleController extends Controller {
	public function InitPage() {
		if(($website = $this->GetWebsite($this->page)) == false) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("article");
		
		if($this->href[0] != "/")
			$this->href = "/".$this->href;
		
		$lastUpdate = $this->db->ArticleLastUpdate($website['id'], $this->href);
		if($lastUpdate != -1 && $lastUpdate < Config::$articleRefreshFreq)
			$contentList = $this->db->LoadArticle($website['id'], $this->href);
		else {
			$parserName = str_replace(array(" ", "-"), "", $website['name'])."Parser";
			$parser = new $parserName(@file_get_contents(str_replace(" ", "+", $website['url'].$this->href)));
			
			$contentList = $parser->GetArticle();
			$this->db->UpdateArticle($website['id'], $this->href, $contentList);
		}
		
		if(!empty($contentList['title']))
			$this->template->setTitle($contentList['title'].' - '.$website['name']);
		else
			$this->template->setTitle(404);
		
		$content = array();
		$content['title'] = $contentList['title'];
		$content['subTitle'] = $contentList['subTitle'];
		$content['bodyText'] = $contentList['bodyText'];
		$content['timestamp'] = $contentList['timestamp'];
		$content['url'] = $website['url'].htmlspecialchars($this->href);
		$this->template->setContent($content);
	}
}
