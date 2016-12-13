<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Jari-Hermann Ernst <jari-hermann.ernst@bad-gmbh.de>, B·A·D GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package jhe_openweathermap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_JheOpenweathermap_Controller_WeatherController extends Tx_Extbase_MVC_Controller_ActionController {


	public function showAction() {

		$this->addCssClass();
		$this->addJqueryLibrary();
		$this->addJsFile();

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

	public function addCssClass(){
		$this->response->addAdditionalHeaderData('
			<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath($this->request->getControllerExtensionKey()) . 'Resources/Public/Css/owfont-regular.css" />
			<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath($this->request->getControllerExtensionKey()) . 'Resources/Public/Css/styles.css?' . time() . '" type="text/css" media="screen" />
		');
	}

	public function addJqueryLibrary(){
		// checks if t3jquery is loaded
		if (t3lib_extMgm::isLoaded('t3jquery')) {
			require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
		}
		// if t3jquery is loaded and the custom Library had been created
		if (T3JQUERY === true) {
			tx_t3jquery::addJqJS();
		} else {
			//if none of the previous is true, you need to include your own library
			//just as an example in this way
			$this->response->addAdditionalHeaderData('
				<script language="JavaScript" src="' . t3lib_extMgm::siteRelPath($this->request->getControllerExtensionKey()) . 'Resources/Public/JavaScript/jquery-1.11.0.min.js"></script>
			');
		}
	}

	public function addJsFile(){
		$this->response->addAdditionalHeaderData('
			<script language="JavaScript" src="' . t3lib_extMgm::siteRelPath($this->request->getControllerExtensionKey()) . 'Resources/Public/JavaScript/jhe_openweathermap.js?' . time() . '"></script>
		');
	}
}
?>