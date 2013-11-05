<?php
require_once("app/controller/Abstract.php");

if(file_exists("config/Config.php"))
	require_once("config/Config.php");

class InstallController extends Controller {
	public function GetPage($step) {
		$this->InitPage($step);
		
		$this->layout->setTitle($this->template->getTitle());
		$this->layout->setContent(array('content'=>$this->template->getHTML()));
		
		return $this->layout->getHTML();
	}
	
	
	public function InitPage($step) {
		$this->template->setTemplate("install/template");
		$this->template->setTitle("Installing LiteNews");
		$content = array();
		
		if($step == NULL)
			$step = 1;
		
		switch($content['step']) {
			case 1:
				if(file_exists("config_sample"))
					rename("config_sample", "config");
				
				break;
			
			case 4:
				Config::UpdateConfig();
				$content['done'] = true;
				break;
		}
		
		$content['step'] = $step;
		$this->template->setContent($content);
	}
}
