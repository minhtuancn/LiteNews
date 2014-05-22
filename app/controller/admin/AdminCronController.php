<?php
class AdminCronController extends AdminController {
	protected function InitDB() {
		$this->db = new AdminSQL;
	}
	
	
	public function InitAdminPage($content) {
		$this->template->setTemplate("admin/cron");
		$this->template->setTitle("Manage cronjobs");
		
		if(isset($_POST['cronUser']) && isset($_POST['cronUserPassword']) && isset($_POST['cronCommand'])) {
			file_put_contents("temp/cron.txt", $_POST['cronCommand'].PHP_EOL);
			ob_start();
			
			// Managing cronjobs temporaly disabled due security issues
			if(false && system("echo ".$_POST['cronUserPassword']."\n | sudo -u ".$_POST['cronUser']." -S crontab -u ".$_POST['cronUser']." ".Config::GetPath("cron/filePath")."temp/cron.txt"))
				$content['cronSuccess'] = true;
			else
				$content['cronFailure'] = true;
			
			ob_end_clean();
			unlink("temp/cron.txt");
		}
		
		$content['cronUser'] = Config::GetPath("cron/user");
		$content['cronFilePath'] = Config::GetPath("cron/filePath");
		$content['cronCommand'] = Config::GetPath("cron/command");
		$content['errorLog'] = explode("\n", file_get_contents(Config::GetPath("cron/errorLog")));
		
		foreach($content['errorLog'] as $errorKey=>$errorValue) {
			if(empty($errorValue))
				unset($content['errorLog'][$errorKey]);
		}
		
		$maxLogSize = Config::GetPath("cron/showErrors");
		if(!empty($maxLogSize) && sizeof($content['errorLog']) > $maxLogSize)
			$content['errorLog'] = array_slice($content['errorLog'], 0, $maxLogSize);
		
		$this->template->setContent($content);
	}
}
