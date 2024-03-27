<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function (): void {

    ExtensionUtility::registerPlugin(
        'JheOpenweathermap',
        'WeatherPlugin',
        'Weather Forecast'
    );

})();