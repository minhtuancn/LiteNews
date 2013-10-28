<?php
class ConfigParser {
	public static function Parse($file) {
		$file = "config/xml/".$file;
		
		if(!file_exists($file))
			return NULL;
		
		$dom = new DOMDocument;
		$dom->loadXML(file_get_contents($file));
		
		$content = array();
		$elements = $dom->getElementsByTagname("config");
		
		foreach($elements as $element)
			$content[] = self::RecursiveParse($element->childNodes);
		
		$content = $content[0];
		
		return $content;
	}
	
	
	private static function RecursiveParse($nodes) {
		$content = array();
		
		foreach($nodes as $node) {
			if($node->nodeType == 3 || $node->nodeType == 8)
				continue;
			
			if($node->hasChildNodes() && $node->childNodes->length > 1)
				$value = self::RecursiveParse($node->childNodes);
			else
				$value = $node->nodeValue;
			
			if(isset($content[$node->nodeName])) {
				if(!is_array($content[$node->nodeName]) || (is_array($value) && !isset($content[$node->nodeName][0])))
					$content[$node->nodeName] = array($content[$node->nodeName]);
				
				$content[$node->nodeName] = array_merge($content[$node->nodeName], array($value));
			}
			else
				$content[$node->nodeName] = $value;
		}
		
		return $content;
	}
}