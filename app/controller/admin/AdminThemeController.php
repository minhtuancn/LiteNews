<?php
class AdminThemeController extends AdminController {
	protected function InitDB() {
		$this->db = new AdminSQL;
	}
	
	
	protected function InitAdminPage($content) {
		$this->UpdateCSS();
		$this->UpdateJS();
		$content['notice'][] = "Theme cache updated successfully";
		parent::InitAdminPage($content);
	}
	
	
	protected function UpdateCSS() {
		$base = "";
		foreach(Config::GetPath("layout/css/path", true) as $file) {
			$base .= file_get_contents("design/css/".$file);
		}
		
		// Strip base before loop for optimization
		$base = str_replace(array("\n", "\r", "	"), "", $base);
		
		array_map("unlink", glob("cache/css/*"));
		
		foreach(Config::GetPath("layout/themes/theme", true) as $theme) {
			$themeName = strtolower(str_replace(" ", "", $theme));
			$css = str_replace(array("\n", "\r", "	"), "", file_get_contents("design/css/".$themeName.".css"));
			$mergedCSS = $base.$css;
			file_put_contents("cache/css/".$themeName.".css", $mergedCSS);
		}
	}
	
	
	protected function UpdateJS() {
		$mergedJS = "";
		
		foreach(Config::GetPath("layout/js/path", true) as $jsFile) {
			$mergedJS .= file_get_contents("design/js/".$jsFile);
		}
		
		file_put_contents("cache/js.js", $mergedJS);
	}
}
