<?php
class StatsController extends IndexController {
	public function InitPage() {
		$this->template->setTemplate("stats");
		$this->template->setTitle("Stats");
		
		$websites = $this->SortByPopularity(Config::$websites);
		
		$this->template->setContent(array('websites'=>$websites));
	}
}
