<?php
require_once("Controller.php");

class LiteNews {
	protected $controller;
	protected $page;
	protected $href;
	
	
	public function __construct($page=NULL, $href=NULL) {
		$this->page = $page;
		$this->href = $href;
		
		if(is_null($this->page))
			$this->controller = new IndexController;
		elseif($this->page == "feedback")
			$this->controller = new FeedbackController;
		elseif($this->page == "settings")
			$this->controller = new SettingsController;
		elseif($this->page == "stats")
			$this->controller = new StatsController;
		elseif(is_null($this->href))
			$this->controller = new ListController;
		else
			$this->controller = new ArticleController;
	}
	
	
	public function __toString() {
		return $this->controller->GetPage($this->page, $this->href);
	}
}
