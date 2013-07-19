<?php
class FeedbackController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("feedback");
		$this->template->setTitle("Send feedback");
		$content = array();
		
		if(isset($_POST['feedback'])) {
			$feedbackType = is_numeric($_POST['feedbackType']) ? $_POST['feedbackType'] : 3;
			
			if($this->db->AddFeedback($feedbackType, $_POST['feedback']))
				$content['sendSuccess'] = true;
			else
				$content['sendFail'] = true;
		}
		
		$this->template->setContent($content);
	}
}
