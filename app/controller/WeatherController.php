<?php
class WeatherController extends Controller {
	public function InitPage() {
		$this->template->setTemplate("weather");
		$this->template->setTitle("Weather");
		$content = array();
		
		$content['locations'] = $this->db->GetLocationNames();
		$content['tabIndex'] = 0;
		
		$this->template->setContent($content);
	}
}
