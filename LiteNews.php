<?php
require_once("app/controller/Abstract.php");

class LiteNews {
	protected $controller;
	protected $page;
	protected $href;
	
	
	public function __construct($page=NULL, $href=NULL) {
		$this->page = $page;
		$this->href = $href;
		
		if(is_null($this->page))
			$this->controller = new IndexController;
		elseif($this->page == "cron")
			$this->controller = new CronController;
		elseif($this->page == "info")
			$this->controller = new InfoController;
		elseif($this->page == "feedback")
			$this->controller = new FeedbackController;
		elseif($this->page == "settings")
			$this->controller = new SettingsController;
		elseif($this->page == "collection")
			$this->controller = new CollectionController;
		elseif($this->page == "admin")
			$this->controller = new AdminController;
		elseif(is_null($this->href))
			$this->controller = new ListController;
		else
			$this->controller = new ArticleController;
	}
	
	
	public function __toString() {
		return $this->controller->GetPage($this->page, $this->href);
	}
}
