<?php
namespace Cyberelk\JheOpenweathermap\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 *
 * @package jhe_openweathermap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class WeatherController extends ActionController {

	public function showAction() {

		// //retrive data from ts settings
		$apikey = $this->settings['openweathermapApiKey'];
		$apiurl = $this->settings['openWeatherMapApiUrl'];
		$apicountry = $this->settings['openWeatherMapApiCountry'];
		$apilang = $this->settings['openWeatherMapApiLang'];
		$apiunits = $this->settings['openWeatherMapApiUnits'];
		$currentzip = $this->settings['defaultZip'];
		$currentcity = $this->settings['defaultCity'];

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
