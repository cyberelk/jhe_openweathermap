<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Cyberelk\JheOpenweathermap\Controller\WeatherController;

(function(){

	ExtensionUtility::configurePlugin(
		'JheOpenweathermap',
		'Weather',
		[WeatherController::class => 'show'],
		[WeatherController::class => 'show']
	);

	$TYPO3_CONF_VARS['FE']['eID_include']['openweathermap'] = 'EXT:jhe_openweathermap/Classes/Ajax/SendRequest.php';
})();
