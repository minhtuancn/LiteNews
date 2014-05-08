<?php
class FeedbackController extends Controller {
	public function InitPage() {
		if(!Config::GetPath("local/feedback/enable")) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("feedback");
		$this->template->setTitle("Send feedback");
		$content = array();
		
		if(isset($_POST['feedback'])) {
			if(!array_key_exists($_POST['feedbackType'], Config::GetPath("local/feedback/feedbackTypes", true)))
				$feedbackType = 1;
			else
				$feedbackType = $_POST['feedbackType'];
			
			if(!empty($_POST['feedback']) && $this->db->FeedbackCount($_SERVER['REMOTE_ADDR']) < Config::GetPath("local/feedback/maxFeedbackPerHour") && $this->db->AddFeedback($feedbackType, $_POST['feedbackEmail'], $_POST['feedback']))
				$content['sendSuccess'] = true;
			else
				$content['sendFail'] = true;
		}
		
		$this->template->setContent($content);
	}
}
