<?php
class CronSQL extends Database {
	public function EldestUpdate() {
		$query = $this->db->prepare("SELECT WebsiteID FROM UpdateTime ORDER BY Timestamp ASC, WebsiteID ASC");
		if(!$query->execute())
			return NULL;
		
		if($query->rowCount() < sizeof(Config::GetPath("website/website", true))) {
			$insert = $this->db->prepare("INSERT IGNORE INTO UpdateTime (WebsiteID, Timestamp) VALUES (?, 0)");
			
			foreach(Config::GetPath("website/website", true) as $website) {
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
		$this->DeleteDuplicates($website, $url);
		
		$insert = $this->db->prepare("INSERT IGNORE INTO Article (WebsiteID, URL, Category, ListTitle, ArticleTitle, SubTitle, Timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)");
		if(!$insert->execute(array($website, $url, $data['category'], trim($data['listTitle']), trim($data['title']), trim($data['subTitle']), trim($data['timestamp']))))
			return false;
		
		$newArticleID = $this->db->lastInsertId("ID");
		$articleParagraph = $this->db->prepare("INSERT INTO ArticleParagraph (ArticleID, Paragraph) VALUES (?, ?)");
		
		foreach($data['bodyText'] as $p) {
			if(!$articleParagraph->execute(array($newArticleID, $p)))
				return false;
		}
	}
	
	
	protected function DeleteDuplicates($websiteID, $url) {
		$duplicates = $this->db->prepare("SELECT ID FROM Article WHERE WebsiteID=? AND URL=?");
		$duplicates->execute(array($websiteID, $url));
		
		while(($articleID = $duplicates->fetchColumn()) != false) {
			$article = $this->db->prepare("DELETE FROM Article WHERE ID=?");
			$article->execute(array($articleID));
			
			$paragraphs = $this->db->prepare("DELETE FROM ArticleParagraph WHERE ArticleID=?");
			$paragraphs->execute(array($articleID));
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
	
	
	public function AddPageCache($params, $data) {
		$check = $this->db->prepare("SELECT ID FROM FPC WHERE Parameters=?");
		$check->execute(array($params));
		
		if($check->fetch()) {
			$query = $this->db->prepare("UPDATE FPC SET Content=:data WHERE Parameters=:params");
		}
		else {
			$query = $this->db->prepare("INSERT INTO FPC (Parameters, Content, Timestamp) VALUES (:params, :data, ".time().")");
		}
		
		$query->bindParam(":data", $data, PDO::PARAM_STR);
		$query->bindParam(":params", $params, PDO::PARAM_STR);
		$query->execute();
	}
}
