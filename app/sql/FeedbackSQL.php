<?php
class FeedbackSQL extends Database {
	public function AddFeedback($type, $email, $content) {
		$query = $this->db->prepare("INSERT INTO Feedback (Type, Email, Content, IP, Timestamp) VALUES (?, ?, ?, ?, UNIX_TIMESTAMP())");
		return $query->execute(array($type, $email, $content, $_SERVER['REMOTE_ADDR']));
	}
	
	
	public function FeedbackCount($ip) {
		// Count feedbacks sent in an hour from same IP
		$timeLimit = time() - 3600;
		
		$query = $this->db->prepare("SELECT COUNT(*) FROM Feedback WHERE IP=? AND Timestamp>?");
		$query->execute(array($ip, $timeLimit));
		return $query->fetchColumn();
	}
}
