<?php
class AdminFPCController extends AdminController {
	protected function InitDB() {
		$this->db = new AdminSQL;
	}
	
	
	protected function InitAdminPage($content) {
		$this->db->TruncateFPC();
		$content['notice'][] = "Full page cache cleared";
		parent::InitAdminPage($content);
	}
}
