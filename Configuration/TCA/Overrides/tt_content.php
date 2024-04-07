<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function (): void {

    ExtensionUtility::registerPlugin(
        'JheOpenweathermap',
        'WeatherPlugin',
        'Weather Forecast'
    );

    // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
    $pluginSignature = 'jheopenweathermap_weatherplugin';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        // FlexForm configuration schema file
        'FILE:EXT:jhe_openweathermap/Configuration/FlexForms/WeatherFlexForm.xml'
    );

})();