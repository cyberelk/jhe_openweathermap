<?php
namespace Cyberelk\JheOpenweathermap\Controller;

use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 *
 * @package jhe_openweathermap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class WeatherController extends ActionController {


	public function showAction() {

		//retrive data from ts settings
		$openweathermapApiKey = $this->settings['openweathermapApiKey'];
		$openWeatherMapApiUrl = $this->settings['openWeatherMapApiUrl'];
		$openWeatherMapApiCountry = $this->settings['openWeatherMapApiCountry'];
		$openWeatherMapApiLang = $this->settings['openWeatherMapApiLang'];
		$openWeatherMapApiUnits = $this->settings['openWeatherMapApiUnits'];

		//get fe_user data to show the correct local weather data
		if($GLOBALS['TSFE']->fe_user && $GLOBALS['TSFE']->fe_user->user['zip']){
			$currentZip = $GLOBALS['TSFE']->fe_user->user['zip'];
		} else {
			$currentZip = $this->settings['defaultZip'];
		}
		if($GLOBALS['TSFE']->fe_user && $GLOBALS['TSFE']->fe_user->user['city']){
			$currentCity = $GLOBALS['TSFE']->fe_user->user['city'];
		} else {
			$currentCity = $this->settings['defaultCity'];
		}

		$jsonArr = array(
			'apikey' => $openweathermapApiKey,
			'apiurl' => $openWeatherMapApiUrl,
			'apicountry' => $openWeatherMapApiCountry,
			'apilang' => $openWeatherMapApiLang,
			'apiunits' => $openWeatherMapApiUnits,
			'currentzip' => $currentZip,
			'currentcity' => $currentCity
		);

		$this->view->assign('weatherDataArr', base64_encode(serialize($jsonArr)));
	}

}
