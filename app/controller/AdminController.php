<?php
class AdminController extends Controller {
	public function InitPage() {
		$content = array('notice'=>array());
		$login = $this->CheckLogin($content);
		
		$this->action = explode("/", $this->href);
		
		if(!$login)
			$this->InitLogin($content);
		else
			$this->InitAdminPage($content);
	}
	
	
	protected function CheckLogin(&$content) {
		$login = false;
		
		if($this->href == "logout") {
			if(isset($_COOKIE['admin']) && $_COOKIE['admin'] == crypt(Config::GetPath("local/admin/password"), "$2a$")) {
				setcookie("admin", "logout", time() - 3600, "/");
				$content['logout'] = true;
			}
		}
		elseif(isset($_COOKIE['admin']) && $_COOKIE['admin'] == crypt(Config::GetPath("local/admin/password"), "$2a$")) {
			// Update expiration when loading page
			setcookie("admin", $_COOKIE['admin'], time() + 3600, "/");
			$login = true;
		}
		elseif(isset($_POST['adminPassword'])) {
			$failAttempts = $this->db->GetAdminLoginFails();
			
			if($failAttempts > Config::GetPath("local/admin/maxLoginAttempts"))
				$content['maxLoginFails'] = true;
			elseif($_POST['adminPassword'] == Config::GetPath("local/admin/password")) {
				setcookie("admin", crypt($_POST['adminPassword'], "$2a$"), time() + 3600, "/");
				$content['notice'][] = "Logged in successfully";
				$login = true;
			}
			else
				$content['loginFail'] = true;
		}
		
		return $login;
	}
	
	
	protected function InitLogin($content) {
		$this->template->setTemplate("admin/login");
		$this->template->setTitle("Admin login");
		$this->template->setContent($content);
	}
	
	
	protected function InitAdminPage($content) {
		$this->template->setTemplate("admin/main");
		$this->template->setTitle("Admin panel");
		
		$content['feedbacks'] = $this->db->GetFeedbacksNum(false);
		$content['unreadFeedbacks'] = $this->db->GetFeedbacksNum(true);
		$content['loads'] = $this->db->GetLoads("");
		$content['visitors'] = $this->db->GetVisitors();
		
		$content['stats'] = $this->db->AddLoads(Config::GetPath("website/website", true));
		usort(
			$content['stats'],
			function($a, $b) {
				if($a['loads'] == $b['loads'])
					return $a['id'] > $b['id'];
				return $a['loads'] < $b['loads'];
			}
		);
		
		$this->template->setContent($content);
	}
}
