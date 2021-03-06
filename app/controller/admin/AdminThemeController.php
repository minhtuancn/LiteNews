<?php
class AdminThemeController extends AdminController {
	protected function InitDB() {
		$this->db = new AdminSQL;
	}
	
	
	protected function InitAdminPage($content) {
		$this->UpdateCSS();
		$this->UpdateJS();
		$this->UpdateSVG();
		$content['notice'][] = "Theme cache updated successfully";
		parent::InitAdminPage($content);
	}
	
	
	protected function UpdateCSS() {
		$base = "";
		foreach(Config::GetPath("layout/css/path", true) as $file) {
			$base .= file_get_contents("design/".$file);
		}
		
		array_map("unlink", glob("cache/css/*"));
		
		foreach(Config::GetPath("layout/themes/theme", true) as $theme) {
			$themeName = strtolower(str_replace(" ", "", $theme));
			$scss = file_get_contents("design/scss/theme/".$themeName.".scss");
			file_put_contents("cache/css/temp.scss", $scss.$base);
            exec("sass --scss cache/css/temp.scss > cache/css/".$themeName.".css");
            unlink("cache/css/temp.scss");
            file_put_contents(
                "cache/css/".$themeName.".css",
                str_replace(array("\n", "\r", "   "), "", file_get_contents("cache/css/".$themeName.".css"))
            );
		}
	}
	
	
	protected function UpdateJS() {
		$mergedJS = "";
		
		foreach(Config::GetPath("layout/js/path", true) as $jsFile) {
			$mergedJS .= file_get_contents("design/js/".$jsFile)."\n";
		}
		
		file_put_contents("cache/js.js", $mergedJS);
	}
	
	
	protected function UpdateSVG() {
		$mergedSVG = "";
		
		foreach(Config::GetPath("website/website", true) as $website) {
			$path = "design/img/logo/";
			
			if(!isset($website['logo']) || !file_exists($path.$website['logo']))
				continue;
			
			$content = file_get_contents($path.$website['logo']);
			$content = str_replace(array("	", "\n", "\r"), "", substr($content, strpos($content, "<svg")));
			$mergedSVG .= str_pad($website['id'], 2, "0", STR_PAD_LEFT).$content."\n";
		}
		
		file_put_contents("cache/logo.svg", $mergedSVG);
	}
}
