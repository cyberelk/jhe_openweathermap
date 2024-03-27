<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile('jhe_openweathermap', 'Configuration/TypoScript', 'Open Weather Map');
