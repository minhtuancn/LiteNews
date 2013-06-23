<?php
class Locale {
	protected $translations;
	
	
	public function __construct($language="en") {
		$this->translations = array();
		
		$file = "locale/".$language.".csv";
		if(file_exists($file)) {
			$csv = @file_get_contents($file);
			
			foreach(str_getcsv($csv, "\n", "\0") as $line) {
				$column = str_getcsv($line, ",", "\"");
				
				if(!empty($column) && !empty($column[0])) {
					$this->translations[$column[0]] = $column[1];
				}
			}
		}
	}
	
	
	public function translate($str) {
		if(array_key_exists($str, $this->translations))
			$str = $this->translations[$str];
		
		return htmlspecialchars($str);
	}
}