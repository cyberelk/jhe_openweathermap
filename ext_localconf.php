<?php
defined('TYPO3') or die();

call_user_func(function(){

	TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		'JheOpenweathermap',
		'WeatherPlugin',
		[
			Cyberelk\JheOpenweathermap\Controller\WeatherController::class => 'show, sendRequest'
		],
		[
			Cyberelk\JheOpenweathermap\Controller\WeatherController::class => 'show, sendRequest'
		]
	);

});
