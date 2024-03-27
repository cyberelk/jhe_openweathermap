<?php
namespace Cyberelk\JheOpenweathermap\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
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
		// $openweathermapApiKey = $this->settings['openweathermapApiKey'];
		// $openWeatherMapApiUrl = $this->settings['openWeatherMapApiUrl'];
		// $openWeatherMapApiCountry = $this->settings['openWeatherMapApiCountry'];
		// $openWeatherMapApiLang = $this->settings['openWeatherMapApiLang'];
		// $openWeatherMapApiUnits = $this->settings['openWeatherMapApiUnits'];

		// //get fe_user data to show the correct local weather data
		// if($GLOBALS['TSFE']->fe_user && $GLOBALS['TSFE']->fe_user->user['zip']){
		// 	$currentZip = $GLOBALS['TSFE']->fe_user->user['zip'];
		// } else {
		// 	$currentZip = $this->settings['defaultZip'];
		// }
		// if($GLOBALS['TSFE']->fe_user && $GLOBALS['TSFE']->fe_user->user['city']){
		// 	$currentCity = $GLOBALS['TSFE']->fe_user->user['city'];
		// } else {
		// 	$currentCity = $this->settings['defaultCity'];
		// }

		// $jsonArr = array(
		// 	'apikey' => $openweathermapApiKey,
		// 	'apiurl' => $openWeatherMapApiUrl,
		// 	'apicountry' => $openWeatherMapApiCountry,
		// 	'apilang' => $openWeatherMapApiLang,
		// 	'apiunits' => $openWeatherMapApiUnits,
		// 	'currentzip' => $currentZip,
		// 	'currentcity' => $currentCity
		// );

		// $this->view->assign('weatherDataArr', base64_encode(serialize($jsonArr)));

	}

	public function sendRequestAction(){

		return 'Willkommen in der richtigen Methode...';

		$weatherDataArr = unserialize(base64_decode(GeneralUtility::_POST('data')));

		$apikey = $weatherDataArr['apikey'];
		$apiurl = $weatherDataArr['apiurl'];
		$apicountry = $weatherDataArr['apicountry'];
		$apilang = $weatherDataArr['apilang'];
		$apiunits = $weatherDataArr['apiunits'];
		$currentzip = $weatherDataArr['currentzip'];
		$currentcity = $weatherDataArr['currentcity'];

		$returnData = array();

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
			$returnData['error'] = false;
			$returnData['city'] = $currentcity;
			$returnData['weather'] = $weather;
			$returnData['temperature'] = $temperature;
			$returnData['temperature_min'] = $temperature_min;
			$returnData['temperature_max'] = $temperature_max;
			$returnData['dayOrNight'] = $suffix;
			$returnData['lang'] = $apilang;
		} else {
			$returnData['error'] = true;

			// Deactivated the sending of the log data in error cases during my vacations
			// Activate after return!
			//$this->sendLogDataToAdmin($request, $response, $jsonObj);
		}

		echo json_encode($returnData);
		exit(0);
	}

	public function sendLogDataToAdmin($request, $response, $jsonObj){
		$errorMailMessage = '
			Request:' . $request . '
			Response: ' . $response . '
			Fehlermessage: '. $jsonObj->message . '
			Fehlercode: ' . $jsonObj->cod;

		mail('teampoint@bad-gmbh.de', 'Fehlermeldung api.openweathermap.org', $errorMailMessage);
	}

	public function convertKelvinToCelsius($temperature){
		if(!is_numeric($temperature)){
			return false;
		}

		$calculatedTemperature = round(($temperature - 273.15));

		return $calculatedTemperature;
	}

}
