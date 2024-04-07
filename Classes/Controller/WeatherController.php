<?php
namespace Cyberelk\JheOpenweathermap\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 *
 * @package jhe_openweathermap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class WeatherController extends ActionController {

	public function showAction() {

		$dataError = false;
		$apikey = '';
		$apiurl = '';
		$apilang = '';
		$apiunits = '';
		$apicountry = '';
		$currentzip = '';
		$currentcity = '';

		// retrieve standard data from ts settings
		if(isset($this->settings['openweathermapApiKey']) && $this->settings['openweathermapApiKey'] != ''
		 && isset($this->settings['openWeatherMapApiUrl']) && $this->settings['openWeatherMapApiUrl'] != ''
		 && isset($this->settings['openWeatherMapApiLang']) && $this->settings['openWeatherMapApiLang'] != ''
		 && isset($this->settings['openWeatherMapApiUnits']) && $this->settings['openWeatherMapApiUnits'] != ''){
			$apikey = $this->settings['openweathermapApiKey'];
			$apiurl = $this->settings['openWeatherMapApiUrl'];
			$apilang = $this->settings['openWeatherMapApiLang'];
			$apiunits = $this->settings['openWeatherMapApiUnits'];
		} else {
			$dataError = true;
		}

		if(isset($this->settings['pluginType']) && $this->settings['pluginType'] == 'typoscript'){
			if(isset($this->settings['openWeatherMapApiCountry']) && $this->settings['openWeatherMapApiCountry'] != ''
			 && isset($this->settings['defaultZip']) && $this->settings['defaultZip'] != ''
			 && isset($this->settings['defaultCity']) && $this->settings['defaultCity'] != ''){
				$apicountry = $this->settings['openWeatherMapApiCountry'];
				$currentzip = $this->settings['defaultZip'];
				$currentcity = $this->settings['defaultCity'];
			} else {
				$dataError = true;
			}
		}

		if(isset($this->settings['pluginType']) && $this->settings['pluginType'] == 'fe_user'){
			// retrieve data from fe_user
			/** @var FrontendUserAuthentication $fe_user */
			$fe_user = $this->request->getAttribute('frontend.user');
			if(!empty($fe_user)
			 && isset($fe_user->user['country']) && $fe_user->user['country'] != ''
			 && isset($fe_user->user['city']) && $fe_user->user['city'] != ''
			 && isset($fe_user->user['zip']) && $fe_user->user['zip'] != ''){
				 $apicountry = $fe_user->user['country'];
				 $currentcity = $fe_user->user['city'];
				 $currentzip = $fe_user->user['zip'];
			 } else {
				$dataError = true;
			 }
		}

		if(isset($this->settings['pluginType']) && $this->settings['pluginType'] == 'plugin'){
			if(isset($this->settings['country']) && $this->settings['country'] != ''
			 && isset($this->settings['city']) && $this->settings['city'] != ''
			 && isset($this->settings['zip']) && $this->settings['zip'] != ''){
				 $apicountry = $this->settings['country'];
				 $currentcity = $this->settings['city'];
				 $currentzip = $this->settings['zip'];
			 } else {
				$dataError = true;
			 }
		}
		
		if($dataError === false) {
			//assemble the openWeatherMap API link
			$request = $apiurl . '?q=' . $currentzip . ',' . $apicountry . '&APPID=' . $apikey;

			//retrieve the actual weather data from openWeatherMap API and decode the jason data
			$response = file_get_contents($request);

			$jsonObj = json_decode($response);

			if($jsonObj->weather && $jsonObj->main && $jsonObj->sys){
				//compile the openWeatherMap data for use in the fluid template
				$weather = $jsonObj->weather; //array of possible data
				$temperature = $this->convertKelvinToCelsius($jsonObj->main->temp);
				$temperature_min = $this->convertKelvinToCelsius($jsonObj->main->temp_min);
				$temperature_max = $this->convertKelvinToCelsius($jsonObj->main->temp_max);

				$now = date('U'); //get current time

				if($now > $jsonObj->sys->sunrise and $now < $jsonObj->sys->sunset){
					$suffix = '-d';
				}else{
					$suffix = '-n';
				}

				//assemble return data array
				$weatherData['dataError'] = false;
				$weatherData['error'] = false;
				$weatherData['city'] = $currentcity;
				$weatherData['weather'] = $weather;
				$weatherData['temperature'] = $temperature;
				$weatherData['temperature_min'] = $temperature_min;
				$weatherData['temperature_max'] = $temperature_max;
				$weatherData['dayOrNight'] = $suffix;
				$weatherData['lang'] = $apilang;
			} else {
				$weatherData['error'] = true;
			}
		} else {
			$weatherData['dataError'] = true;
		}

		$this->view->assign('weatherData', $weatherData);
	}

	public function convertKelvinToCelsius($temperature){
		if(!is_numeric($temperature)){
			return false;
		}
		$calculatedTemperature = round(($temperature - 273.15));
		return $calculatedTemperature;
	}

}
