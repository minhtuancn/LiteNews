<?php
require_once("Config.php");

class Template {
	private $template;
	private $title;
	private $content;
	
	
	public function __construct($template=NULL, $title=NULL, $content=NULL) {
		$this->template = $template;
		$this->title = $title;
		$this->content = $content;
	}
	
	
	public function setTemplate($template) {
		$this->template = $template;
	}
	
	
	public function setTitle($title) {
		$this->title = htmlspecialchars($title);
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
		
		if(file_exists("template/".$this->template.".phtml"))
			include("template/".$this->template.".phtml");
		
		return str_replace("\n", "", ob_get_clean());
	}
}
