<?php
class AdminFeedbackController extends AdminController {
	protected function InitDB() {
		$this->db = new AdminSQL;
	}
	
	
	protected function InitAdminPage($action, $content) {
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
		$content['feedbackTypes'] = Config::GetPath("local/feedback/feedbackTypes/type", true);
		$this->template->setContent($content);
	}
}
