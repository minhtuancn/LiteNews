<?php
class IndexController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("main");
		$this->template->setTitle("Valitse sivusto");
		
		$content['websites'] = Config::$websites;
		$content['userSettings'] = Config::$allowUserSettings;
		$this->template->setContent($content);
	}
}
