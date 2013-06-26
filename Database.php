<?php
require_once("Config.php");

class Database {
	protected $db;
	
	
	public function __construct($host, $username, $password, $database) {
		$this->db = new PDO('mysql:host='.$host.';dbname='.$database.';charset=utf8', $username, $password);
	}
	
	
	public function __destruct() {
		$timestamp = new DateTime();
		$timestamp->modify("-".Config::$articleRefreshFreq." minutes");
		$query = $this->db->prepare("SELECT ID FROM Article WHERE LastUpdate<?");
		
		if(!$query->execute(array($timestamp->getTimestamp())))
			return;
		
		foreach($query->fetchAll() as $article) {
			$this->db->prepare("DELETE FROM ArticleParagraph WHERE ArticleID=?")->execute(array($article['ID']));
			$this->db->prepare("DELETE FROM Article WHERE ID=?")->execute(array($article['ID']));
		}
	}
	
	
	public function UpdateLog($url) {
		$this->db->prepare("INSERT INTO Log (IP, URL) VALUES (?, ?)")->execute(array($_SERVER['REMOTE_ADDR'], $url));
	}
	
	
	public function ListLastUpdate($website) {
		$query = $this->db->prepare("SELECT LastUpdate FROM TitleListUpdate WHERE WebsiteID=?");
		$lastUpdate = new DateTime();
		
		if(!$query->execute(array($website)) || $query->rowCount() == 0) {
			$this->db->prepare("INSERT INTO TitleListUpdate (WebsiteID, LastUpdate) VALUES (?, ?)")->execute(array($website, $lastUpdate->getTimestamp()));
			return -1;
		}
		else
			$lastUpdate->setTimestamp($query->fetchColumn());
		
		$currentTime = new DateTime();
		$difference = $lastUpdate->diff($currentTime);
		return $difference->days*24*60 + $difference->h*60 + $difference->i;
	}
	
	
	public function ArticleLastUpdate($website, $url) {
		$query = $this->db->prepare("SELECT LastUpdate FROM Article WHERE WebsiteID=? AND URL=?");
		
		if(!$query->execute(array($website, $url)) || $query->rowCount() == 0)
			return -1;
		
		$currentTime = new DateTime();
		$lastUpdate = new DateTime();
		$lastUpdate->setTimestamp($query->fetchColumn());
		$difference = $lastUpdate->diff($currentTime);
		return $difference->days*24*60 + $difference->h*60 + $difference->i;
	}
	
	
	public function LoadTitles($website) {
		$titles = array();
		
		$query = $this->db->prepare("SELECT Title, URL FROM TitleList WHERE WebsiteID=? ORDER BY ID ASC");
		if(!$query->execute(array($website)))
			return $titles;
		
		$rows = array_map("unserialize", array_unique(array_map("serialize", $query->fetchAll())));
		
		foreach($rows as $row)
			$titles[] = array('title'=>$row['Title'], 'url'=>$row['URL']);
		
		return $titles;
	}
	
	
	public function LoadArticle($website, $url) {
		$article = array();
		
		$query = $this->db->prepare("SELECT ID, Title, SubTitle FROM Article WHERE WebsiteID=? AND URL=?");
		if(!$query->execute(array($website, $url)))
			return $article;
		
		$query = $query->fetch(PDO::FETCH_ASSOC);
		$article['title'] = $query['Title'];
		$article['subTitle'] = $query['SubTitle'];
		$article['bodyText'] = array();
		
		$paragraphs = $this->db->prepare("SELECT Paragraph FROM ArticleParagraph WHERE ArticleID=?");
		if(!$paragraphs->execute(array($query['ID'])))
			return $article;
		
		foreach($paragraphs->fetchAll(PDO::FETCH_ASSOC) as $p)
			$article['bodyText'][] = $p['Paragraph'];
		
		return $article;
	}
	
	
	public function UpdateTitles($website, $data) {
		if(!$this->db->prepare("DELETE FROM TitleList WHERE WebsiteID=?")->execute(array($website)))
			return;
	
		foreach($data as $title) {
			if(!$this->db->prepare("INSERT INTO TitleList (WebsiteID, Title, URL) VALUES (?, ?, ?)")->execute(array($website, $title['title'], $title['url'])))
				return;
		}
		
		$timestamp = new DateTime();
		$this->db->prepare("UPDATE TitleListUpdate SET LastUpdate=? WHERE WebsiteID=?")->execute(array($timestamp->getTimestamp(), $website));
	}
	
	
	public function UpdateArticle($website, $url, $data) {
		$articleID = $this->db->prepare("SELECT ID FROM Article WHERE URL=?");
		if(!$articleID->execute(array($url)))
			return;
		else
			$articleID = $articleID->fetchColumn();
		
		if(!$this->db->prepare("DELETE FROM Article WHERE ID=?")->execute(array($articleID)))
			return;
		
		if(!$this->db->prepare("DELETE FROM ArticleParagraph WHERE ArticleID=?")->execute(array($articleID)))
			return;
		
		$timestamp = new DateTime();
		if(!$this->db->prepare("INSERT INTO Article (WebsiteID, URL, Title, SubTitle, LastUpdate) VALUES (?, ?, ?, ?, ?)")->execute(array($website, $url, $data['title'], $data['subTitle'], $timestamp->getTimestamp())))
			return;
		
		$newArticleID = $this->db->lastInsertId("ID");
		$articleParagraph = $this->db->prepare("INSERT INTO ArticleParagraph (ArticleID, Paragraph) VALUES (?, ?)");
		
		foreach($data['bodyText'] as $p) {
			if(!$articleParagraph->execute(array($newArticleID, $p)))
				return;
		}
	}
	
	
	public function GetLoads($websiteName) {
		$query = $this->db->prepare("SELECT COUNT(*) FROM Log WHERE URL LIKE ?");
		$query->execute(array("/%page=".str_replace(" ", "%20", $websiteName)."%"));
		$loads = $query->fetch();
		if($loads != false)
			return $loads[0];
		else
			return NULL;
	}
	
	
	public function GetBrowserData() {
		//$query = $this->db->prepare("")
	}
}
?>
