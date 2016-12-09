<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
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