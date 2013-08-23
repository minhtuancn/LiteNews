<?php
class IndexController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("main");
		$this->template->setTitle("Choose website");
		
		$content['websites'] = Config::$websites;
		if(Config::$websitesSortByPopularity)
			$content['websites'] = $this->SortByPopularity($content['websites']);
		
		$content['feedback'] = Config::$enableFeedback;
		$content['userSettings'] = Config::$allowUserSettings;
		$this->template->setContent($content);
	}
	
	
	protected function SortByPopularity($websites) {
		$websites = $this->db->AddLoads($websites);
		
		usort(
			$websites,
			function($a, $b) {
				if($a['loads'] == $b['loads'])
					return $a['id'] > $b['id'];
				return $a['loads'] < $b['loads'];
			}
		);
		
		return $websites;
	}
}
