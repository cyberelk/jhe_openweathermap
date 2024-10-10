<?php
namespace Cyberelk\JheOpenweathermap\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 *
 * @package jhe_openweathermap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class WeatherController extends ActionController {

	public function showAction(): ResponseInterface {

		// Initialize weather data array with defaults to avoid undefined index errors.
		$weatherData = [
			'dataError' => false,
			'apiKeyError' => false,
			'apiUrlError' => false,
			'feuserError' => false,
			'city' => '',
			'weather' => [],
			'temperature' => 0,
			'temperature_min' => 0,
			'temperature_max' => 0,
			'dayOrNight' => '',
			'lang' => ''
		];

		// Test for given Openweathermap API KEY within the ts constant settings
		if(!isset($this->settings['openweathermapApiKey']) || empty($this->settings['openweathermapApiKey'])) {
			$weatherData['apiKeyError'] = true;
		}

		// Attempt to configure the API settings and load the weather data.
		if ($this->configureApiSettings($weatherData)) {
			$this->loadAndPrepareWeatherData($weatherData);
		}

		// Assign the prepared or defaulted weather data to the view.
		$this->view->assign('weatherData', $weatherData);

		return $this->htmlResponse();
	}

	private function configureApiSettings(&$weatherData) {
		$settings = $this->initializeSettings();

		if (!$this->applyStandardApiSettings($settings)) {
			$weatherData['dataError'] = true;
			return false;
		}

		if (!$this->applyPluginSpecificSettings($settings, $weatherData)) {
			$weatherData['dataError'] = true;
			return false;
		}

		// Verify if all necessary settings have been defined.
		if (in_array('', $settings, true)) {
			$weatherData['dataError'] = true;
			return false;
		}

		// Settings configured successfully, proceed with these settings.
		$weatherData += $settings; // Merge settings into weather data array for future use.
		return true;
	}

	private function initializeSettings() {
		return [
			'apikey' => '',
			'apiurl' => '',
			'apiversion' => '',
			'apilang' => '',
			'apiunits' => '',
			'apicountry' => '',
			'currentzip' => '',
			'currentcity' => ''
		];
	}

	private function applyStandardApiSettings(&$settings) {
		$requiredApiSettings = [
			'apikey' => 'openweathermapApiKey',
			'apiurl' => 'openWeatherMapApiUrl',
			'apiversion' => 'openweathermapApiVersion',
			'apilang' => 'openWeatherMapApiLang',
			'apiunits' => 'openWeatherMapApiUnits'
		];

		return $this->checkAndSetSettings($requiredApiSettings, $settings, $this->settings);
	}

	private function applyPluginSpecificSettings(&$settings, &$weatherData) {
		$pluginType = $this->settings['pluginType'] ?? '';
		$pluginTypesSettingsMap = $this->getPluginTypesSettingsMap($pluginType);

		if ($pluginType === 'fe_user') {
			/** @var FrontendUserAuthentication $fe_user */
			$fe_user = $this->request->getAttribute('frontend.user');
			$source = $fe_user ? $fe_user->user : [];

			// Provide an error state if no user is currently logged in
			if(empty($source)){
				$weatherData['feuserError'] = true;
			}

		} else {
			$source = $this->settings;
		}

		// If pluginType is recognized, check and set the required settings.
		if (array_key_exists($pluginType, $pluginTypesSettingsMap)) {
			return $this->checkAndSetSettings($pluginTypesSettingsMap[$pluginType], $settings, $source);
		}

		// If pluginType is unrecognized, consider it a configuration error.
		return false;
	}

	private function getPluginTypesSettingsMap($pluginType) {
		return [
			'typoscript' => [
				'apicountry' => 'openWeatherMapApiCountry',
				'currentzip' => 'defaultZip',
				'currentcity' => 'defaultCity'
			],
			'fe_user' => [
				'apicountry' => 'country',
				'currentcity' => 'city',
				'currentzip' => 'zip'
			],
			'plugin' => [
				'apicountry' => 'country',
				'currentcity' => 'city',
				'currentzip' => 'zip'
			]
		];
	}

	private function loadAndPrepareWeatherData(&$weatherData): void {
		$apiResponse = $this->fetchWeatherDataFromApi($weatherData);

		if (!$apiResponse || !$this->validateApiResponse($apiResponse)) {
			$weatherData['apiUrlError'] = true;
			return;
		}

		// Populate weather data array with values derived from the API response.
		$weatherData['city'] = $weatherData['currentcity'];
		$weatherData['weather'] = $apiResponse->weather;
		$weatherData['temperature'] = $this->convertKelvinToCelsius($apiResponse->main->temp);
		$weatherData['temperature_min'] = $this->convertKelvinToCelsius($apiResponse->main->temp_min);
		$weatherData['temperature_max'] = $this->convertKelvinToCelsius($apiResponse->main->temp_max);
		$weatherData['dayOrNight'] = $this->getTimeOfDaySuffix($apiResponse);
		$weatherData['lang'] = $weatherData['apilang'];
	}

	private function fetchWeatherDataFromApi($settings) {
		// Determine the appropriate API request URL based on API version.
		$requestUrl = $this->buildApiRequestUrl($settings);
		if (empty($requestUrl)) {
			return null;
		}

		$response = file_get_contents($requestUrl);
		return json_decode($response);
	}

	private function validateApiResponse($apiResponse) {
		return isset($apiResponse->weather, $apiResponse->main, $apiResponse->sys);
	}

	private function buildApiRequestUrl($settings) {
		// Logic to construct API request URL based on the apiversion and other settings...
		// Similar to the original snippet, but encapsulated within this method for clarity.
		if($settings['apiversion'] == '2.5'){
			$requestUrl = $settings['apiurl'] . $settings['apiversion'] . '/weather?q=' . $settings['currentzip'] . ',' . $settings['apicountry'] . '&APPID=' . $settings['apikey'];
		}
		if($settings['apiversion'] == '3.0'){
			//TODO: Test this settings with api 3.0 which has to be paid for
			$locationRequest = 'http://api.openweathermap.org/geo/1.0/zip?zip=13053,de&appid=' . $settings['apikey'];
			$locationResponse = file_get_contents($locationRequest);
			$locationsJsonObj = json_decode($locationResponse);
			// https://api.openweathermap.org/data/3.0/onecall?lat=33.44&lon=-94.04&appid={API key}
			$requestUrl = $settings['apiurl'] . $settings['apiversion'] . '/onecall?lat=' . $locationsJsonObj->lat . '&lon=' . $locationsJsonObj->lon . '&appid=' . $settings['apikey'];
		}

		return $requestUrl;
	}

	public function convertKelvinToCelsius($temperature) {
		if (!is_numeric($temperature)) {
			return false;
		}
		return round($temperature - 273.15);
	}

	public function checkAndSetSettings(array $requiredKeys, array &$settings, $source) {
		foreach ($requiredKeys as $key => $settingKey) {
			if (!isset($source[$settingKey]) || $source[$settingKey] == '') {
				return false;
			}
			$settings[$key] = $source[$settingKey];
		}
		return true;
	}

	function getTimeOfDaySuffix($jsonObj) {
		$now = time();

		if ($now > $jsonObj->sys->sunrise && $now < $jsonObj->sys->sunset) {
			return '-d'; // Daytime
		} else {
			return '-n'; // Nighttime
		}
	}

}