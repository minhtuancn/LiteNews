<?php
class CNETParser extends Parser {
    public function GetTitles() {
        $titles = array();
        
        foreach($this->dom->getElementsByTagName('item') as $item) {
            $title = $item->getElementsByTagName('title')->item(0)->nodeValue;
            $url = $item->getElementsByTagName('link')->item(0)->nodeValue;
            $titles[] = array('title'=>$title, 'url'=>$url);
        }
        
        return $titles;
    }
    
    public function GetArticle() {
        $content = $this->InitArticle();
        
        $container = $this->dom->getElementsByTagName('article');
        if($container->length == 0) {
            return $content;
        }
        $container = $container->item(0);
        
        $title = $container->getElementsByTagName('h1');
        if($title->length == 0) {
            return $content;
        }
        $content['title'] = $title->item(0)->nodeValue;
        
        $date = $container->getElementsByTagName('time');
        if($date->length == 0) {
            return $content;
        }
        $date = $date->item(0)->getAttribute('class');
        $content['timestamp'] = strtotime($date);
        
        $imageContainer = $container->getElementsByTagName('figure');
        if($imageContainer->length > 0) {
            $image = $imageContainer->item(0);
            if(strpos($image->getAttribute('class'), "image") !== false) {
                $image = $image->getElementsByTagName('img')->item(0);
                $content['image'] = $image->getAttribute('src');
            }
        }
        
        $text = $container->getElementsByTagName('p');
        
        foreach($text as $p) {
            if(!$p->hasAttribute('class')) {
                $content['bodyText'][] = $p->nodeValue;
            }
            elseif(strpos($p->getAttribute('class'), "article-dek") !== false) {
                $content['subTitle'] = $p->nodeValue;
            }
        }
        
        return $content;
    }
}
