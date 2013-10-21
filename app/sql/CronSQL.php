<?php
class CronSQL extends Database {
	public function EldestUpdate() {
		$query = $this->db->prepare("SELECT WebsiteID FROM UpdateTime ORDER BY Timestamp ASC, WebsiteID ASC");
		if(!$query->execute())
			return NULL;
		
		if($query->rowCount() < sizeof(Config::$websites)) {
			$insert = $this->db->prepare("INSERT IGNORE INTO UpdateTime (WebsiteID, Timestamp) VALUES (?, 0)");
			
			foreach(Config::$websites as $website) {
				$insert->execute(array($website['id']));
			}
			
			return NULL;
		}
		
		return $query->fetchColumn();
	}
	
	
	public function RefreshUpdateTime($websiteID) {
		$this->db->prepare("UPDATE UpdateTime SET Timestamp=? WHERE WebsiteID=?")->execute(array(time(), $websiteID));
	}
	
	
	public function AddArticle($website, $url, $data) {
		$insert = $this->db->prepare("INSERT IGNORE INTO Article (WebsiteID, URL, ListTitle, ArticleTitle, SubTitle, Timestamp) VALUES (?, ?, ?, ?, ?, ?)");
		if(!$insert->execute(array($website, $url, trim($data['listTitle']), trim($data['title']), trim($data['subTitle']), trim($data['timestamp']))))
			return false;
		
		$newArticleID = $this->db->lastInsertId("ID");
		$articleParagraph = $this->db->prepare("INSERT INTO ArticleParagraph (ArticleID, Paragraph) VALUES (?, ?)");
		
		foreach($data['bodyText'] as $p) {
			if(!$articleParagraph->execute(array($newArticleID, $p)))
				return false;
		}
	}
	
	
	public function DeleteArticles($websiteID) {
		$articles = $this->db->prepare("SELECT ID FROM Article WHERE WebsiteID=?");
		if(!$articles->execute(array($websiteID)))
			return false;
		
		$deleteArticle = $this->db->prepare("DELETE FROM Article WHERE ID=?");
		$deleteParagraphs = $this->db->prepare("DELETE FROM ArticleParagraph WHERE ArticleID=?");
		
		while(($id = $articles->fetchColumn()) != false) {
			$deleteArticle->execute(array($id));
			$deleteParagraphs->execute(array($id));
		}
	}
}
