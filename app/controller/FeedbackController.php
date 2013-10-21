<?php
class FeedbackController extends Controller {
	public function InitPage() {
		if(!Config::$enableFeedback) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("feedback");
		$this->template->setTitle("Send feedback");
		$content = array();
		
		if(isset($_POST['feedback'])) {
			if(!array_key_exists($_POST['feedbackType'], Config::$feedbackTypes))
				$feedbackType = 1;
			else
				$feedbackType = $_POST['feedbackType'];
			
			if($this->db->AddFeedback($feedbackType, $_POST['feedback']))
				$content['sendSuccess'] = true;
			else
				$content['sendFail'] = true;
		}
		
		$this->template->setContent($content);
	}
}
