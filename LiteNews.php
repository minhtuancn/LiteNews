<?php
require_once("app/controller/Abstract.php");

ini_set("error_reporting", E_ALL | E_STRICT);
set_error_handler(array("Controller", "LogPHPError"));

class LiteNews {
	protected $controller;
	protected $page;
	protected $href;
	
	
	public function __construct($page=NULL, $href=NULL) {
		$this->page = $page;
		$this->href = $href;
		
		// TODO: Clean this horrible piece of code
		foreach(Config::GetPath("controller/controller", true) as $controller) {
			$condition = $controller['condition'];
			if(!isset($condition['page']))
				$condition['page'] = NULL;
			if(!isset($condition['href']))
				$condition['href'] = NULL;
			
			if((!isset($condition['page']) || $page === $condition['page'] || ($page == NULL && is_string($condition['page'])))
				&& (!isset($condition['href']) || $href === $condition['href'] || ($href == NULL && is_string($condition['href'])))
			) {
				$controllerName = $controller['name']."Controller";
				$this->controller = new $controllerName;
				break;
			}
		}
	}
	
	
	public function __toString() {
		return $this->controller->GetPage($this->page, $this->href);
	}
}
