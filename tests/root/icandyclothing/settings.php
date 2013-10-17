<?php
use jenga\conf\Settings;

$conf = [
	'DOMAIN' => 'local.icandyclothing.com',
	'DATABASES' => [
		'default' => [
			'host' => 'localhost',
			'user' => '',
			'pass' => '',
			'port' => ''
		],
		'mongo' => [
			'host' => 'localhost',
			'name' => 'local',
			'user' => '',
			'pass' => '',
			'port' => null
		]
	],
	'INSTALLED_APPS' => [
		'thumbnails'
	]
];
$conf['BASE_URL'] = 'http://' . $conf['DOMAIN'];
$conf['STATIC_PATH'] = JENGA_BASE_PATH . '/static';
$conf['STATIC_URL'] = '/static';
# Thumbnails App
$conf['THUMBNAILS_IMAGE_PATH'] = $conf['STATIC_PATH'] . '/icandyclothing/images';
$conf['THUMBNAILS_IMAGE_URL'] = $conf['STATIC_URL'] . '/icandyclothing';
Settings::set($conf);