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
	
	
	public function AddArticle($website, $url, $data, $updateArticle=false) {
		if($updateArticle !== false) {
            $query = $this->db->prepare("UPDATE Article SET Category=:Category, ListTitle=:ListTitle, ArticleTitle=:ArticleTitle, SubTitle=:SubTitle, Timestamp=:Timestamp WHERE ID=:ID");
            $query->bindParam(":ID", $updateArticle, PDO::PARAM_INT);
            $deleteParagraphs = $this->db->prepare("DELETE FROM ArticleParagraph WHERE ID=?");
            $deleteParagraphs->execute(array($updateArticle));
        }
        else {
            $query = $this->db->prepare("INSERT IGNORE INTO Article (WebsiteID, URL, Category, ListTitle, ArticleTitle, SubTitle, Timestamp) VALUES (:WebsiteID, :URL, :Category, :ListTitle, :ArticleTitle, :SubTitle, :Timestamp)");
            $query->bindParam(":WebsiteID", $website, PDO::PARAM_INT);
            $query->bindParam(":URL", $url, PDO::PARAM_STR);
        }
        
        $query->bindParam(":Category", $data['category'], PDO::PARAM_INT);
        $query->bindParam(":ListTitle", $data['listTitle'], PDO::PARAM_STR);
        $query->bindParam(":ArticleTitle", $data['title'], PDO::PARAM_STR);
        $query->bindParam(":SubTitle", $data['subTitle'], PDO::PARAM_STR);
        $query->bindParam(":Timestamp", $data['timestamp'], PDO::PARAM_INT);
        
		if(!$query->execute()) {
			return false;
        }
		
        $articleID = $updateArticle === false ? $this->db->lastInsertId("ID") : $updateArticle;
		$articleParagraph = $this->db->prepare("INSERT INTO ArticleParagraph (ArticleID, Paragraph) VALUES (?, ?)");
		
		foreach($data['bodyText'] as $p) {
			if(!$articleParagraph->execute(array($articleID, $p))) {
				return false;
			}
		}
        
        return $articleID;
	}
	
    
    public function ArticleExists($websiteID, $url) {
        $id = $this->db->prepare("SELECT ID FROM Article WHERE WebsiteID=? AND URL=?");
        $id->execute(array($websiteID, $url));
        return $id->fetchColumn();
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
