<?php
class ListSQL extends Database {
	public function LoadTitles($websites, $offset=0) {
		$titles = array();
		
		$websitesStr = implode(',', $websites);
		$query = $this->db->prepare("
			SELECT WebsiteID, ListTitle, URL, Timestamp
			FROM Article
			WHERE WebsiteID IN (".$websitesStr.")
			ORDER BY Timestamp DESC LIMIT :offset, :limit");
		$query->bindParam(":offset", intval($offset), PDO::PARAM_INT);
		$query->bindParam(":limit", intval(Config::GetPath("local/listLimit")), PDO::PARAM_INT);
		
		if(!$query->execute())
			return $titles;
		
		foreach($query->fetchAll() as $row)
			$titles[] = array('website'=>$row['WebsiteID'], 'title'=>$row['ListTitle'], 'url'=>$row['URL'], 'timestamp'=>$row['Timestamp']);
		
		return $titles;
	}
}
