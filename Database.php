<?php
require_once("Config.php");

class Database {
	protected $db;
	
	
	public function __construct($host, $username, $password, $database) {
		$this->db = new PDO('mysql:host='.$host.';dbname='.$database.';charset=utf8', $username, $password);
	}
	
	
	public function __destruct() {
		if(Config::$logExpire > 0) {
			$query = $this->db->prepare("DELETE FROM Log WHERE Timestamp<?");
			$query->execute(array(time() - 60 * Config::$logExpire));
		}
	}
	
	
	public function AddLog($data) {
		return $this->db
			->prepare("INSERT INTO Log (IP, Timestamp, URL) VALUES (?, UNIX_TIMESTAMP(), ?)")
			->execute(array($_SERVER['REMOTE_ADDR'], $data));
	}
	
	
	public function EldestUpdate() {
		$query = $this->db->prepare("SELECT WebsiteID, Timestamp FROM UpdateTime ORDER BY Timestamp ASC, WebsiteID ASC");
		if(!$query->execute())
			return NULL;
		
		if($query->rowCount() < sizeof(Config::$websites)) {
			$insert = $this->db->prepare("INSERT IGNORE INTO UpdateTime (WebsiteID, Timestamp) VALUES (?, 0)");
			
			foreach(Config::$websites as $website) {
				$insert->execute(array($website['id']));
			}
			
			return NULL;
		}
		
		return $query->fetch();
	}
	
	
	public function RefreshUpdateTime($websiteID) {
		$this->db->prepare("UPDATE UpdateTime SET Timestamp=UNIX_TIMESTAMP() WHERE WebsiteID=?")->execute(array($websiteID));
	}
	
	
	public function LoadCollection($limit, $websites) {
		$websitesStr = "";
		foreach($websites as $websiteKey => $website) {
			$websitesStr .= $websiteKey != 0 ? "," : " ";
			$websitesStr .= $this->db->quote($website, PDO::PARAM_INT);
		}
		
		if($limit > 0) {
			$query = $this->db->prepare("SELECT WebsiteID, ListTitle, URL, Timestamp FROM Article WHERE WebsiteID IN (".$websitesStr.") ORDER BY Timestamp DESC LIMIT :limit");
			$query->bindParam(":limit", intval($limit), PDO::PARAM_INT);
		}
		else
			$query = $this->db->prepare("SELECT WebsiteID, ListTitle, URL, Timestamp FROM Article WHERE WebsiteID IN (".$websitesStr.") ORDER BY Timestamp DESC");
		
		$query->execute();
		
		$list = array();
		foreach($query->fetchAll() as $article)
			$list[] = array('website'=>$article['WebsiteID'], 'title'=>$article['ListTitle'], 'url'=>$article['URL'], 'timestamp'=>$article['Timestamp']);
		
		return $list;
	}
	
	
	public function LoadTitles($website, $limit) {
		$titles = array();
		
		if($limit > 0) {
			$query = $this->db->prepare("SELECT ListTitle, URL, Timestamp FROM Article WHERE WebsiteID=:WebsiteID ORDER BY Timestamp DESC LIMIT :limit");
			$query->bindParam(":limit", intval($limit), PDO::PARAM_INT);
		}
		else
			$query = $this->db->prepare("SELECT ListTitle, URL, Timestamp FROM Article WHERE WebsiteID = :WebsiteID ORDER BY Timestamp DESC");
		
		$query->bindParam(":WebsiteID", $website, PDO::PARAM_INT);
		
		if(!$query->execute())
			return $titles;
		
		
		foreach($query->fetchAll() as $row)
			$titles[] = array('title'=>$row['ListTitle'], 'url'=>$row['URL'], 'timestamp'=>$row['Timestamp']);
		
		return $titles;
	}
	
	
	public function LoadArticle($website, $url) {
		$article = array(
			'title'=>NULL,
			'subTitle'=>NULL,
			'bodyText'=>array(),
			'timestamp'=>0
		);
		
		$query = $this->db->prepare("SELECT ID, ArticleTitle, SubTitle, Timestamp FROM Article WHERE WebsiteID=? AND URL=?");
		if(!$query->execute(array($website, $url)))
			return $article;
		
		$query = $query->fetch(PDO::FETCH_ASSOC);
		$article['title'] = $query['ArticleTitle'];
		$article['subTitle'] = $query['SubTitle'];
		$article['timestamp'] = $query['Timestamp'];
		
		$paragraphs = $this->db->prepare("SELECT Paragraph FROM ArticleParagraph WHERE ArticleID=?");
		if(!$paragraphs->execute(array($query['ID'])))
			return $article;
		
		foreach($paragraphs->fetchAll(PDO::FETCH_ASSOC) as $p)
			$article['bodyText'][] = $p['Paragraph'];
		
		return $article;
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
	
	
	public function AddFeedback($type, $content) {
		$query = $this->db->prepare("INSERT INTO Feedback (Type, Content, IP, Timestamp) VALUES (?, ?, ?, UNIX_TIMESTAMP())");
		return $query->execute(array($type, $content, $_SERVER['REMOTE_ADDR']));
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
		$query = $this->db->prepare("SELECT * FROM Feedback ORDER BY Timestamp DESC, ID DESC LIMIT :offset, :limit");
		$query->bindParam(":offset", intval($offset), PDO::PARAM_INT);
		$query->bindParam(":limit", intval($limit), PDO::PARAM_INT);
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
	
	
	public function GetAdminLoginFails() {
		$timestamp = time() - 3600;
		$query = $this->db->prepare("SELECT COUNT(*) FROM Log WHERE URL='/admin/loginfail' AND Timestamp>?");
		$query->execute(array($timestamp));
		return $query->fetchColumn();
	}
}
?>
