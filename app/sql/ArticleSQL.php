<?php
class ArticleSQL extends Database {
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
        $article['id'] = $query['ID'];
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
}
