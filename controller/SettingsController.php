<?php
class SettingsController extends Controller {
	public function InitPage() {
		if(!Config::$allowUserSettings) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("settings");
		$this->template->setTitle("Settings");
		
		$content = array();
		
		foreach(Config::$defaultUserSettings as $settingName => $settingValue) {
			if(isset($_POST[$settingName])) {
				if(setcookie(Config::$userSettingsCookie."[".$settingName."]", $_POST[$settingName], time() + Config::$settingsCookieExpire)) {
					$content['saveSuccess'] = true;
					$content['saveNotice'] = "Settings succesfully saved";
					$_COOKIE[Config::$userSettingsCookie][$settingName] = $_POST[$settingName];
				}
				else
					$content['saveNotice'] = "Saving settings failed";
			}
		}
		
		$content['bgColorOptions'] = array();
		foreach(Config::$bgColors as $bgColor)
			$content['bgColorOptions'][] = $bgColor['name'];
		
		$this->template->setContent($content);
	}
}
