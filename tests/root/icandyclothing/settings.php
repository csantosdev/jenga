<?php
use jenga\conf\Settings;
use jenga\db\connections\Connection;

$conf = [
	'DOMAIN' => 'local.icandyclothing.com',
	'DATABASES' => [
		'default' => [
			'type' => Connection::SQL_BACKEND_TYPE,
			'host' => 'localhost',
			'user' => '',
			'pass' => '',
			'port' => ''
		],
		'mongo' => [
			'type' => Connection::MONGO_BACKEND_TYPE,
			'host' => 'localhost',
			'name' => 'local',
			'user' => null,
			'pass' => null,
			'port' => 27017
		]
	],
	'INSTALLED_APPS' => [
		'thumbnails'
	]
];
$conf['MEDIA_ROOT'] = JENGA_BASE_PATH . '/static/media';
$conf['BASE_URL'] = 'http://' . $conf['DOMAIN'];
$conf['STATIC_PATH'] = JENGA_BASE_PATH . '/static';
$conf['STATIC_URL'] = '/static';
# Thumbnails App
$conf['THUMBNAILS_IMAGE_PATH'] = $conf['STATIC_PATH'] . '/icandyclothing/images';
$conf['THUMBNAILS_IMAGE_URL'] = $conf['STATIC_URL'] . '/icandyclothing';
Settings::set($conf);