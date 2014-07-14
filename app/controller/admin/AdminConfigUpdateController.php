<?php
class AdminConfigUpdateController extends AdminController {
	protected function InitDB() {
		$this->db = new AdminSQL;
	}
	
	
	protected function InitAdminPage($content) {
		Config::UpdateConfig();
		$content['notice'][] = "XML configuration updated successfully";
		parent::InitAdminPage($content);
	}
}
