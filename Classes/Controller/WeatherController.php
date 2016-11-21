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

		//simple test data which should be made dynamical during the development process

		$openweathermapAPIkey = 'c81fecb0e191e18ad078746ccaba3cc9';

		//$plzBonn = '53225';
		$country = 'de';


		$currentUser = $GLOBALS['TSFE']->fe_user->user;

		//t3lib_utility_Debug::debug($currentUser);

		$currentUserCity = $GLOBALS['TSFE']->fe_user->user['city'];
		$currentUserZip = $GLOBALS['TSFE']->fe_user->user['zip'];



		$request = 'http://api.openweathermap.org/data/2.5/weather?zip=' . $currentUserZip . ',' . $country . '&APPID=' . $openweathermapAPIkey . '&lang=de&units=metric';

		//t3lib_utility_Debug::debug($request);

		$response = file_get_contents($request);

		$jsonObj = json_decode($response);


		$weather = $jsonObj->weather; //array of possible data
		//t3lib_utility_Debug::debug($weather);
		$temperature = round($jsonObj->main->temp);
		$temperature_min = $jsonObj->main->temp_min;
		$temperature_max = $jsonObj->main->temp_max;

		$this->view->assign('city', $currentUserCity);
		$this->view->assign('weather', $weather);
		$this->view->assign('temperature', $temperature);
		$this->view->assign('temperature_min', $temperature_min);
		$this->view->assign('temperature_max', $temperature_max);







	}


}