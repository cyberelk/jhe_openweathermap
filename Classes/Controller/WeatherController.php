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
class Tx_JheOpenweathermap_Controller_WeatherController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {


	public function showAction() {

		$this->addCssClass();

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
			<link rel="stylesheet" type="text/css" href="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($this->request->getControllerExtensionKey()) . 'Resources/Public/Css/owfont-regular.css" />
			<link rel="stylesheet" type="text/css" href="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($this->request->getControllerExtensionKey()) . 'Resources/Public/Css/styles.css?' . time() . '" type="text/css" media="screen" />
		');
	}

}
?>