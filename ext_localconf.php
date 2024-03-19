<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'Weather' => 'show',
	),
	// non-cacheable actions
	array(
		'Weather' => 'show',
	)
);

$TYPO3_CONF_VARS['FE']['eID_include']['openweathermap'] = 'EXT:jhe_openweathermap/Classes/Ajax/SendRequest.php';
?>