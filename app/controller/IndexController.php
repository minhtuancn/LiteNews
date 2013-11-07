<?php
class IndexController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("main");
		$this->template->setTitle("Choose website");
		
		if(!isset($_COOKIE['visited'])) {
			$content['showInfo'] = true;
			setcookie("visited", 1, time() + Config::GetPath("local/userSettings/cookieExpire") * 60);
		}
		else
			$content['showInfo'] = false;
		
		$websites = Config::GetPath("website/website", true);
		if(Config::GetPath("local/sortByPopularity"))
			$websites = $this->SortByPopularity($websites);
		
		foreach($websites as $website) {
			$content['websites'][$website['language']][] = $website;
		}
		
		$content['feedback'] = Config::GetPath("local/feedback/enable");
		$content['userSettings'] = Config::GetPath("local/userSettings/enable");
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
