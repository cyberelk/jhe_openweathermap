<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'OpenWeatherMap',
	'description' => 'Displays the local weather data from https://openweathermap.org/',
	'category' => 'plugin',
	'author' => 'Jari-Herman Ernst',
	'author_email' => 'webdev@jhernst.de',
	'state' => 'beta',
	'version' => 'v12.0.2',
	'constraints' => array(
		'depends' => array(
			'typo3' => '12.4.0-12.4.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
