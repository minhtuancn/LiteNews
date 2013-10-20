<?php
class SettingsController extends Controller {
	public function InitDB() {
		$this->db = NULL;
	}
	
	
	public function InitPage() {
		if(!Config::$allowUserSettings) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("settings");
		$this->template->setTitle("Settings");
		
		$content = array();
		
		if(isset($_POST['collection'])) {
			$collection = array();
			
			foreach(Config::$websites as $website) {
				if(isset($_POST['collection_'.$website['id']]))
					$collection[] = $website['id'];
			}
			
			$_POST['collection'] = serialize($collection);
		}
		
		foreach(Config::$defaultUserSettings as $settingName => $settingValue) {
			if(isset($_POST[$settingName])) {
				if(setcookie(Config::$userSettingsCookie."[".$settingName."]", $_POST[$settingName], time() + Config::$settingsCookieExpire * 60)) {
					$content['saveSuccess'] = true;
					$content['saveNotice'] = "Settings succesfully saved";
					$_COOKIE[Config::$userSettingsCookie][$settingName] = $_POST[$settingName];
				}
				else
					$content['saveNotice'] = "Saving settings failed";
			}
		}
		
		$this->layout->setLocale(self::GetUserSetting("lang"));
		$this->template->setLocale(self::GetUserSetting("lang"));
		
		$this->template->setContent($content);
	}
}
