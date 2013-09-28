<?php
class AdminController extends Controller {
	public function InitPage() {
		$this->db = new AdminSQL;
		
		$content = array();
		$login = $this->CheckLogin($content);
		
		$action = explode("/", $this->href);
		
		if(!$login)
			$this->InitLogin($content);
		elseif($action[0] == NULL)
			$this->InitMain($content);
		elseif($action[0] == "feedback")
			$this->InitFeedback($action, $content);
		else
			$this->InitErrorPage();
	}
	
	
	protected function CheckLogin(&$content) {
		$login = false;
		
		if($this->href == "logout") {
			if(isset($_COOKIE['admin']) && $_COOKIE['admin'] == crypt(Config::$adminPassword, "$2a$")) {
				setcookie("admin", "logout", time() - 3600, "/");
				$content['logout'] = true;
			}
		}
		elseif(isset($_COOKIE['admin']) && $_COOKIE['admin'] == crypt(Config::$adminPassword, "$2a$")) {
			$login = true;
		}
		elseif(isset($_POST['adminPassword'])) {
			$failAttempts = $this->db->GetAdminLoginFails();
			
			if($failAttempts > Config::$adminMaxLoginAttempts)
				$content['maxLoginFails'] = true;
			elseif($_POST['adminPassword'] == Config::$adminPassword) {
				setcookie("admin", crypt($_POST['adminPassword'], "$2a$"), time() + 3600, "/");
				$content['loginSuccess'] = true;
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
	
	
	protected function InitMain($content) {
			$this->template->setTemplate("admin/main");
			$this->template->setTitle("Admin panel");
			
			$content['feedbacks'] = $this->db->GetFeedbacksNum(false);
			$content['unreadFeedbacks'] = $this->db->GetFeedbacksNum(true);
			$content['loads'] = $this->db->GetLoads("");
			$content['visitors'] = $this->db->GetVisitors();
			
			$content['stats'] = $this->db->AddLoads(Config::$websites);
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
	
	
	protected function InitFeedback($action, $content) {
		$this->template->setTemplate("admin/feedback");
		$this->template->setTitle("Feedbacks");
		
		if(isset($action[1]) && is_numeric($action[1]) && $action[1] > 0)
			$page = $action[1] - 1;
		else
			$page = 0;
		
		
		if(isset($_POST['feedbackPage']) && is_numeric($_POST['feedbackPage']) && $_POST['feedbackPage'] > 0 && $_POST['feedbackPage'] != $page + 1) {
			header("Location: feedback/".$_POST['feedbackPage']);
			return;
		}
		
		$content['feedbackCurrentPage'] = $page + 1;
		$content['feedbackPages'] = ceil($this->db->GetFeedbacksNum(false) / 10);
		$feedbacks = $this->db->GetFeedbacks(10, $page * 10);
		
		if(isset($_POST['feedbackDeleteMode'])) {
			$deleteMode = $_POST['feedbackDeleteMode'];
			$feedbacksDeleted = 0;
			
			switch($deleteMode) {
				case 1:
					foreach($feedbacks as $feedback) {
						if(isset($_POST['feedback_'.$feedback['ID']])) {
							$this->db->DeleteFeedback($feedback['ID']);
							++$feedbacksDeleted;
						}
					}
					break;
				
				case 2:
					foreach($feedbacks as $feedback) {
						$this->db->DeleteFeedback($feedback['ID']);
						++$feedbacksDeleted;
					}
					break;
				
				case 3:
					$feedbacksDeleted = $this->db->DeleteAllFeedbacks();
					break;
			}
			
			if($feedbacksDeleted > 0) {
				$feedbacks = $this->db->GetFeedbacks(10, $page * 10);
				$content['feedbacksDeleted'] = $feedbacksDeleted;
			}
		}
		
		$content['feedback'] = $feedbacks;
		$this->template->setContent($content);
	}
}
