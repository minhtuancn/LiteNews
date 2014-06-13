<?php
class AdminSQL extends Database {
	public function AddLoads($websites) {
		$query = $this->db->prepare("SELECT COUNT(*) FROM Log WHERE URL LIKE ?");
		
		foreach($websites as &$website) {
			$query->execute(array("/".str_replace(" ", "+", $website['name'])."%"));
			$website['loads'] = $query->fetchColumn();
		}
		
		return $websites;
	}
	
	
	public function GetLoads($websiteName) {
		$query = $this->db->prepare("SELECT COUNT(*) FROM Log WHERE URL LIKE ?");
		$query->execute(array("/".str_replace(" ", "+", $websiteName)."%"));
		$loads = $query->fetch();
		if(is_array($loads))
			return $loads[0];
		else
			return NULL;
	}
	
	
	public function GetVisitors() {
		$query = $this->db->prepare("SELECT COUNT(DISTINCT IP) FROM Log");
		$query->execute();
		return $query->fetchColumn();
	}
	
	
	public function ClearWebsiteData($id) {
		$articles = $this->db->prepare("SELECT ID FROM Article WHERE WebsiteID=?");
		$articles->execute(array($id));
		$articles = $articles->fetchAll();
		
		$deleteParagraphs = $this->db->prepare("DELETE FROM ArticleParagraph WHERE ArticleID=?");
		foreach($articles as $article) {
			$deleteParagraphs->execute(array($article));
		}
		
		$deleteArticles = $this->db->prepare("DELETE FROM Article WHERE WebsiteID=?");
		$deleteArticles->execute(array($id));
		
		$updateTimestamp = $this->db->prepare("UPDATE UpdateTime SET Timestamp=0 WHERE WebsiteID=?");
		$updateTimestamp->execute(array($id));
	}
	
	
	public function GetFeedbacksNum($unreadOnly) {
		if($unreadOnly)
			$query = $this->db->prepare("SELECT COUNT(*) FROM Feedback WHERE Viewed=0");
		else
			$query = $this->db->prepare("SELECT COUNT(*) FROM Feedback");
		
		$query->execute();
		return $query->fetchColumn();
	}
	
	
	public function GetFeedbacks($limit, $offset) {
		$limit = intval($limit);
		$offset = intval($offset);
		$query = $this->db->prepare("SELECT * FROM Feedback ORDER BY Timestamp DESC, ID DESC LIMIT :offset, :limit");
		$query->bindParam(":offset", $offset, PDO::PARAM_INT);
		$query->bindParam(":limit", $limit, PDO::PARAM_INT);
		$query->execute();
		$feedbacks = $query->fetchAll();
		
		$update = $this->db->prepare("UPDATE Feedback SET Viewed=1 WHERE Viewed=0 AND ID=?");
		foreach($feedbacks as $feedback) {
			$update->execute(array($feedback['ID']));
		}
		
		return $feedbacks;
	}
	
	
	public function DeleteFeedback($id) {
		$query = $this->db->prepare("DELETE FROM Feedback WHERE ID=?");
		$query->execute(array($id));
	}
	
	
	public function DeleteAllFeedbacks() {
		$query = $this->db->prepare("DELETE FROM Feedback");
		$query->execute();
		return $query->rowCount();
	}
	
	
	public function GetAdminLoginFails() {
		$timestamp = time() - 3600;
		$query = $this->db->prepare("SELECT COUNT(*) FROM Log WHERE URL='/admin/loginfail' AND Timestamp>?");
		$query->execute(array($timestamp));
		return $query->fetchColumn();
	}
}
