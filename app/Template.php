<?php
require_once("app/Config.php");
require_once("app/controller/Abstract.php");
require_once("app/Language.php");

class Template extends Controller {
	protected $locale;
	protected $template;
	protected $title;
	protected $content;
	protected $block;
	
	
	public function __construct($locale="en", $template=NULL, $title=NULL, $content=NULL) {
		$this->locale = new Language($locale);
		
		$this->template = $template;
		$this->title = $title;
		$this->content = $content;
		$this->block = NULL;
	}
	
	
	protected function __($str) {
		return $this->locale->translate($str);
	}
	
	
	protected function getURL($path=NULL) {
		return Config::GetPath("local/baseURL").str_replace(" ", "+", $path);
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
	
	public function getCSS() {
		$files = array();
		$themes = array_map(
			function($theme) {
				return strtolower(str_replace(" ", "", $theme));
			},
			Config::GetPath("layout/themes/theme", true)
		);
		$theme = $themes[Controller::GetUserSetting("theme")];
		
		if(Config::GetPath("local/mergeCSS")) {
			$files[] = "cache/css/".$theme.".css";
		}
		else {
			foreach(Config::GetPath("layout/css/path", true) as $path) {
				$files[] = "design/css/".$path;
			}
			$files[] = "design/css/".$theme.".css";
		}
		
		$html = "";
		foreach($files as $file) {
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getURL($file).'" media="screen" />';
		}
		
		return $html;
	}
	
	public function getJS() {
		$files = array();
		
		if(Config::GetPath("local/mergeJS")) {
			$files[] = "cache/js.js";
		}
		else {
			foreach(Config::GetPath("layout/js/path", true) as $path) {
				$files[] = "design/js/".$path;
			}
		}
		
		$html = "";
		foreach($files as $file) {
			$html .= '<script src="'.$this->getURL($file).'"></script>';
		}
		
		return $html;
	}
	
	public function getBlock($name, $data=NULL) {
		$currentBlock = $this->block;
		$this->block = $data;
		$html = $this->getHTML($name);
		$this->block = $currentBlock;
		return $html;
	}
	
	public function getHTML($blockName=NULL) {
		$template = $this->template;
		if($blockName != NULL)
			$template = "block/".$blockName;
		
		ob_start();
		
		if(file_exists("design/template/".$template.".phtml"))
			include("design/template/".$template.".phtml");
		
		return str_replace(array("\n", "\r", "\t"), "", ob_get_clean());
	}
}
