<?php
class AdminWebsiteController extends AdminController {
	protected function InitDB() {
		$this->db = new AdminSQL;
	}
	
	
	public function InitAdminPage($content) {
		$this->template->setTemplate("admin/website");
		$this->template->setTitle("Clear website data");
		
		$content['deleteSuccess'] = false;
		
		foreach(Config::GetPath("website/website", true) as $website) {
			if(isset($_POST['website_'.$website['id']])) {
				$this->db->ClearWebsiteData($website['id']);
				$content['deleteSuccess'] = true;
			}
		}
		
		$this->template->setContent($content);
	}
}
