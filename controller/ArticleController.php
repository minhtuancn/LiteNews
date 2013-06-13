<?php
require_once("Parser.php");

class ArticleController extends Controller {
	public function InitPage() {
		if(($website = $this->GetWebsite($this->page)) == false) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("article");
		
		$lastUpdate = $this->db->ArticleLastUpdate($website['name'], $this->href);
		if($lastUpdate < Config::$articleRefreshFreq && $lastUpdate != -1)
			$contentList = $this->db->LoadArticle($website['name'], $this->href);
		else {
			$parser = new Parser($website['name'], @file_get_contents(str_replace(" ", "+", $website['url'].$this->href)));
			$contentList = $parser->GetArticle();
			$this->db->UpdateArticle($website['name'], $this->href, $contentList);
		}
		
		$this->template->setTitle($contentList['title'].' - '.$website['name']);
		
		$content = array();
		$content['title'] = $contentList['title'];
		$content['subTitle'] = $contentList['subTitle'];
		$content['bodyText'] = $contentList['bodyText'];
		$content['url'] = $website['url'].htmlspecialchars($this->href);
		$this->template->setContent($content);
	}
}
