<?php
class WeatherSQL extends Database {
	public function GetLocationNames() {
		$query = $this->db->prepare("SELECT ListTitle FROM Article WHERE WebsiteID=14 ORDER BY ListTitle COLLATE utf8_swedish_ci ASC");
		$query->execute();
		return $query->fetchAll(PDO::FETCH_COLUMN);
	}
	
	
	public function GetGeoData() {
		$query = $this->db->prepare("SELECT * FROM GeoCity");
		$query->execute();
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	public function GetWeather($city) {;
		$content = array();
		
		$articleQuery = $this->db->prepare("SELECT * FROM Article WHERE WebsiteID=14 AND ListTitle=?");
		$articleQuery->execute(array($city));
		$article = $articleQuery->fetch(PDO::FETCH_ASSOC);
		
		$content = array(
			'title'=>$article['ListTitle'],
			'timestamp'=>0,
			'bodyText'=>array()
		);
		
		$contentQuery = $this->db->prepare("SELECT Paragraph FROM ArticleParagraph WHERE ArticleID=?");
		$contentQuery->execute(array($article['ID']));
		
		foreach($contentQuery->fetchAll(PDO::FETCH_ASSOC) as $p) {
			$content['bodyText'][] = $p['Paragraph'];
		}
		
		return $content;
	}
}
