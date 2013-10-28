<?php
require_once("config/Config.php");
require_once("app/controller/Abstract.php");
require_once("app/Language.php");

class Template {
	protected $locale;
	protected $template;
	protected $title;
	protected $content;
	
	
	public function __construct($locale="en", $template=NULL, $title=NULL, $content=NULL) {
		$this->locale = new Language($locale);
		
		$this->template = $template;
		$this->title = $title;
		$this->content = $content;
	}
	
	
	protected function __($str) {
		return $this->locale->translate($str);
	}
	
	
	protected function getURL($path=NULL) {
		return Config::GetPath("local/baseURL").$path;
	}
	
	
	public function setLocale($locale) {
		$this->locale = new Language($locale);
	}
	
	
	public function setTemplate($template) {
		$this->template = $template;
	}
	
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	
	public function getTemplate() {
		return $this->template;
	}
	
	
	public function getTitle() {
		return $this->title;
	}
	
	
	public function getContent() {
		return $this->content;
	}
	
	
	public function getHTML() {
		ob_start();
		
		if(file_exists("design/template/".$this->template.".phtml"))
			include("design/template/".$this->template.".phtml");
		
		return str_replace(array("\n", "\r", "\t"), "", ob_get_clean());
	}
}
