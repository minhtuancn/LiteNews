<?php
require_once("app/parser/Abstract.php");

class ArticleController extends Controller {
	public function InitPage() {
		if(($website = $this->GetWebsiteByName($this->page)) == false) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("article");
		
		$this->href = str_replace(" ", "+", $this->href);
		if($this->href[0] != "/")
			$this->href = "/".$this->href;
		
		$contentList = $this->db->LoadArticle($website['id'], $this->href);
		
		if(!empty($contentList['title']))
			$this->template->setTitle($contentList['title'].' - '.$website['name']);
		else
			$this->template->setTitle(404);
		
		$content = $contentList;
		$content['url'] = $website['url'].htmlspecialchars($this->href);
		$this->template->setContent($content);
	}
}
