<?php
class FeedbackSQL extends Database {
	public function AddFeedback($type, $content) {
		$query = $this->db->prepare("INSERT INTO Feedback (Type, Content, IP, Timestamp) VALUES (?, ?, ?, UNIX_TIMESTAMP())");
		return $query->execute(array($type, $content, $_SERVER['REMOTE_ADDR']));
	}
}
