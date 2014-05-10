<?php
class ListSQL extends Database {
	public function LoadTitles($website) {
		$titles = array();
		
		$query = $this->db->prepare("SELECT ListTitle, URL, Timestamp FROM Article WHERE WebsiteID=:WebsiteID ORDER BY Timestamp DESC LIMIT :limit");
		$query->bindParam(":limit", intval(Config::GetPath("local/listLimit")), PDO::PARAM_INT);
		$query->bindParam(":WebsiteID", $website, PDO::PARAM_INT);
		
		if(!$query->execute())
			return $titles;
		
		
		foreach($query->fetchAll() as $row)
			$titles[] = array('title'=>$row['ListTitle'], 'url'=>$row['URL'], 'timestamp'=>$row['Timestamp']);
		
		return $titles;
	}
}
