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

		//include css file for owfont
		$this->response->addAdditionalHeaderData('<link rel="stylesheet" type="text/css" href="' . t3lib_extMgm::siteRelPath($this->request->getControllerExtensionKey()) . 'Resources/Public/Css/owfont-regular.css" />');

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

		//assemble the openWeatherMap API link
		$request = $openWeatherMapApiUrl . '?zip=' . $currentZip . ',' . $openWeatherMapApiCountry . '&APPID=' . $openweathermapApiKey . '&lang=' . $openWeatherMapApiLang . '&units=' . $openWeatherMapApiUnits;

		//retrieve the actual weather data from openWeatherMap API and decode the jason data
		$response = file_get_contents($request);
		$jsonObj = json_decode($response);

		//compile the openWeatherMap data for use in the fluid template
		$weather = $jsonObj->weather; //array of possible data
		$temperature = round($jsonObj->main->temp);
		$temperature_min = $jsonObj->main->temp_min;
		$temperature_max = $jsonObj->main->temp_max;

		$now = date('U'); //get current time

		if($now > $jsonObj->sys->sunrise and $now < $jsonObj->sys->sunset){
			$suffix = '-d';
		}else{
			$suffix = '-n';
		}

		//assign the data to the fluid template
		$this->view->assign('city', $currentCity);
		$this->view->assign('weather', $weather);
		$this->view->assign('temperature', $temperature);
		$this->view->assign('temperature_min', $temperature_min);
		$this->view->assign('temperature_max', $temperature_max);
		$this->view->assign('dayOrNight', $suffix);
	}
}