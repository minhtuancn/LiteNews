<?php
class SettingsController extends Controller {
	public function InitDB() {
		$this->db = NULL;
	}
	
	
	public function InitPage() {
		if(!Config::GetPath("local/userSettings/enable")) {
			$this->InitErrorPage();
			return;
		}
		
		$this->template->setTemplate("settings");
		$this->template->setTitle("Settings");
		
		$content = array();
		
		if(isset($_POST['collection'])) {
			$collection = array();
			
			foreach(Config::GetPath("website/website", true) as $website) {
				if(isset($_POST['collection_'.$website['id']]))
					$collection[] = $website['id'];
			}
			
			$_POST['collection'] = serialize($collection);
		}
		
		foreach(Config::GetPath("local/userSettings/default", true) as $settingName => $settingValue) {
			if(isset($_POST[$settingName])) {
				if(setcookie(Config::GetPath("local/userSettings/cookie")."[".$settingName."]", $_POST[$settingName], time() + Config::GetPath("local/userSettings/cookieExpire") * 60)) {
					$content['saveSuccess'] = true;
					$content['saveNotice'] = "Settings succesfully saved";
					$_COOKIE[Config::GetPath("local/userSettings/cookie")][$settingName] = $_POST[$settingName];
				}
				else
					$content['saveNotice'] = "Saving settings failed";
			}
		}
		
		$content['locales'] = Config::GetPath("local/locales/locale", true);
		$content['themes'] = Config::GetPath("local/themes/theme", true);
		
		$this->layout->setLocale(self::GetUserSetting("lang"));
		$this->template->setLocale(self::GetUserSetting("lang"));
		
		$this->template->setContent($content);
	}
}
