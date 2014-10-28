<?php
class IndexController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("main");
		
		$websites = Config::GetPath("website/website", true);
		if(Config::GetPath("local/sortByPopularity"))
			$websites = $this->SortByPopularity($websites);
		
		foreach($websites as $website) {
			if(!isset($website['indexExclude'])) {
				$content['websites'][$website['language']][] = $website;
			}
		}
		
		if(key($content['websites']) != $this->GetUserSetting("lang"))
			$content['websites'] = array_reverse($content['websites']);
		
		$content['tabIndex'] = 0;
		
		$content['feedback'] = Config::GetPath("local/feedback/enable");
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
