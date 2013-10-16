<?php
class InfoController extends Controller {
	public function InitDB() {
		$this->db = NULL;
	}
	
	
	public function InitPage() {
		$this->template->setTemplate("info");
		$this->template->setTitle("Using LiteNews");
	}
}
