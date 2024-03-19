<?php
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');
//require_once(t3lib_extMgm::extPath('jhe_vofstin') . 'Classes/Libraries/IXR_Library.inc.php');

Class SendRequest extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin {

	public function sendRequest(){

		$weatherDataArr = unserialize(base64_decode(\TYPO3\CMS\Core\Utility\GeneralUtility::_POST('data')));

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
$sendRequest = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('SendRequest');
$sendRequest->sendRequest();
?>
