<?php
class IndexSQL extends Database {
	public function AddLoads($websites) {
		$query = $this->db->prepare("SELECT COUNT(*) FROM Log WHERE URL LIKE ?");
		
		foreach($websites as &$website) {
			$query->execute(array("/".str_replace(" ", "+", $website['name'])."%"));
			$website['loads'] = $query->fetchColumn();
		}
		
		return $websites;
	}
}
